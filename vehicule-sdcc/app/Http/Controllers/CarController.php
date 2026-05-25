<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Demande;
use App\Models\User;
use App\Notifications\VehicleChangedNotification;
use App\Services\OptionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CarController extends Controller
{
    public function __construct(
        private readonly OptionsService $optionsService
    ) {
        $this->middleware('auth');
    }

    // Show all cars with optimized eager loading
    public function index()
    {
        $cars = Car::with(['demandes' => function ($query) {
            $query->where('status', 'approved')->latest();
        }])->get();
        
        $vehicleStatusOptions = $this->optionsService->vehicleStatuses();
        $vehicleAvailabilityOptions = $this->optionsService->vehicleAvailabilityTypes();
        
        return view('cars.index', compact('cars', 'vehicleStatusOptions', 'vehicleAvailabilityOptions'));
    }

    // Get single car (for API/edit modal)
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return response()->json([
            'car' => $car,
        ]);
    }

    public function available(Request $request)
    {
        try {
            // 1. Récupérer la date avec fallback à aujourd'hui si absente
            $dateInput = $request->input('date');
            if (!$dateInput) {
                $dateInput = Carbon::now()->toDateString();
            }

            // Valider le format de la date
            try {
                $date = Carbon::parse($dateInput);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'cars' => [],
                    'message' => 'Format de date invalide.',
                    'date' => null,
                ]);
            }

            $user = Auth::user();
            
            // 2. Determine which vehicle availability types are acceptable for the selected date
            // - Weekdays (Mon-Fri): Only show 'both' (always available)
            // - Weekends (Sat-Sun): Show 'both' (always available) + 'weekend' (weekend-only)
            $allowedAvailability = $date->isWeekend()
                ? ['weekend', 'both']
                : ['both'];

            // 3. Récupérer les voitures réservées pour cette date
            $reservedCarIds = Demande::query()
                ->whereDate('start_date', $date->toDateString())
                ->whereNotNull('car_id')
                ->whereIn('status', $this->optionsService->reservationBlockingStatuses())
                ->pluck('car_id')
                ->unique()
                ->values()
                ->toArray();

            // 4. Construire la requête de base
            $query = Car::query()
                ->where('status', 'disponible')
                ->whereIn('availability_type', $allowedAvailability)
                ->when(!empty($reservedCarIds), fn ($q) => $q->whereNotIn('id', $reservedCarIds));

            // ========== ZONE FILTERING ==========
            // Admins voient tous les véhicules; employés ne voient que les véhicules de leur zone
            $zoneWarning = null;
            $hasZone = true;

            if ($user && method_exists($user, 'isEmployee') && $user->isEmployee()) {
                $zone = method_exists($user, 'getZone') ? $user->getZone() : null;
                
                if (!$zone) {
                    // Aucune zone assignée: afficher un warning mais retourner tous les véhicules disponibles
                    $hasZone = false;
                    $zoneWarning = 'Vous n\'avez pas de zone de planification assignée. Affichage des véhicules disponibles en mode général.';
                } else {
                    // Filtrer par zone
                    $zoneVehicleIds = $zone->cars()
                        ->pluck('cars.id')
                        ->toArray();

                    // Si la zone a des véhicules assignés, les utiliser; sinon fallback
                    if (!empty($zoneVehicleIds)) {
                        $query->whereIn('id', $zoneVehicleIds);
                    } else {
                        // Zone sans véhicules - fallback à tous les véhicules disponibles
                        Log::warning("Zone {$zone->id} ({$zone->name}) has no vehicles assigned for user {$user->id}");
                        $zoneWarning = 'Aucun véhicule n\'est assigné à votre zone. Affichage des véhicules disponibles en mode général.';
                    }
                }
            }
            // ====================================

            $cars = $query->orderBy('name')->get(['id', 'name', 'matricule', 'availability_type']);

            // Debug: Verify availability_type is correctly included in response
            if ($cars->isNotEmpty()) {
                Log::debug('CarController::available() response', [
                    'date' => $date->toDateString(),
                    'day_type' => $date->isWeekend() ? 'weekend' : 'weekday',
                    'allowed_availability_types' => $allowedAvailability,
                    'vehicle_count' => $cars->count(),
                    'first_vehicle' => $cars->first()->toArray(),
                ]);
            }

            return response()->json([
                'success' => true,
                'date' => $date->toDateString(),
                'day_type' => $date->isWeekend() ? 'weekend' : 'weekday',
                'cars' => $cars,
                'warning' => $zoneWarning,
                'has_zone' => $hasZone,
            ]);
        } catch (\Exception $e) {
            // Toujours retourner un JSON valide sans erreur 500
            Log::error('CarController@available error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'cars' => [],
                'message' => 'Erreur lors du chargement des véhicules disponibles.',
                'date' => null,
            ]);
        }
    }

    // Show create form
    public function create()
    {
        $vehicleStatusOptions = $this->optionsService->vehicleStatuses();
        $vehicleAvailabilityOptions = $this->optionsService->vehicleAvailabilityTypes();
        return view('cars.create', compact('vehicleStatusOptions', 'vehicleAvailabilityOptions'));
    }

    // Store a new car
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'matricule' => 'required|string|unique:cars,matricule|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'km' => 'required|integer|min:0',
            'status' => 'required|in:' . implode(',', array_keys($this->optionsService->vehicleStatuses())),
            'availability_type' => 'required|in:' . implode(',', array_keys($this->optionsService->vehicleAvailabilityTypes())),
        ]);

        try {
            $car = DB::transaction(function () use ($validated) {
                $car = Car::create($validated);
                
                // Broadcast creation to all users for real-time sync
                try {
                    broadcast(new \App\Events\CarCreated($car))->toOthers();
                } catch (Throwable $e) {
                    Log::debug('Broadcasting not available: ' . $e->getMessage());
                }
                
                return $car;
            });

            try {
                $this->notifyVehicleChange('ajoute', $validated['name']);
            } catch (Throwable $e) {
                Log::warning('notifyVehicleChange failed (store)', ['error' => $e->getMessage()]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Véhicule ajouté avec succès!',
                    'car' => $car,
                ], 201);
            }

            return redirect()->route('cars.index')->with('success', 'Véhicule ajouté avec succès!');
        } catch (Throwable $e) {
            Log::error('Car creation failed', ['error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du véhicule',
                ], 500);
            }
            
            return back()->with('error', 'Erreur lors de la création du véhicule');
        }
    }

    // Update a car
    public function edit($id)
    {
        $car = Car::findOrFail($id);
        $vehicleStatusOptions = $this->optionsService->vehicleStatuses();
        $vehicleAvailabilityOptions = $this->optionsService->vehicleAvailabilityTypes();
        return view('cars.edit', compact('car', 'vehicleStatusOptions', 'vehicleAvailabilityOptions'));
    }

    // Update a car
    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'matricule' => 'required|string|max:255|unique:cars,matricule,' . $id,
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'km' => 'required|integer|min:0',
            'status' => 'required|in:' . implode(',', array_keys($this->optionsService->vehicleStatuses())),
            'availability_type' => 'required|in:' . implode(',', array_keys($this->optionsService->vehicleAvailabilityTypes())),
        ]);

        try {
            $updatedCar = DB::transaction(function () use ($car, $validated) {
                $previousStatus = $car->status;
                $previousAvailability = $car->availability_type;
                
                $car->update($validated);
                
                // Broadcast update to all users for real-time sync
                try {
                    // If status or availability changed, broadcast availability change
                    if ($previousStatus !== $validated['status'] || $previousAvailability !== $validated['availability_type']) {
                        broadcast(new \App\Events\AvailabilityChanged($car, $previousStatus, $previousAvailability))->toOthers();
                    } else {
                        // Otherwise broadcast general update
                        broadcast(new \App\Events\CarUpdated($car))->toOthers();
                    }
                } catch (Throwable $e) {
                    Log::debug('Broadcasting not available: ' . $e->getMessage());
                }
                
                return $car;
            });

            try {
                $this->notifyVehicleChange('modifie', $validated['name']);
            } catch (Throwable $e) {
                Log::warning('notifyVehicleChange failed (update)', ['car_id' => $car->id ?? null, 'error' => $e->getMessage()]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Véhicule modifié avec succès!',
                    'car' => $updatedCar,
                ]);
            }

            return redirect()->route('cars.index')->with('success', 'Véhicule modifié avec succès!');
        } catch (Throwable $e) {
            Log::error('Car update failed', ['car_id' => $id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la modification du véhicule',
                ], 500);
            }
            
            return back()->with('error', 'Erreur lors de la modification du véhicule');
        }
    }

    // Change car availability via PATCH with real-time broadcasting
    public function updateAvailability(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|required|in:' . implode(',', array_keys($this->optionsService->vehicleStatuses())),
            'availability_type' => 'sometimes|required|in:' . implode(',', array_keys($this->optionsService->vehicleAvailabilityTypes())),
        ]);

        try {
            $updatedCar = DB::transaction(function () use ($car, $validated) {
                $previousStatus = $car->status;
                $previousAvailability = $car->availability_type;
                
                $car->update($validated);

                // Broadcast availability change to all users for real-time calendar update
                try {
                    broadcast(new \App\Events\AvailabilityChanged(
                        $car,
                        $previousStatus,
                        $previousAvailability
                    ))->toOthers();
                } catch (Throwable $e) {
                    Log::debug('Broadcasting not available: ' . $e->getMessage());
                }
                
                return $car;
            });

            try {
                $this->notifyVehicleChange('disponibilite_modifie', $car->name);
            } catch (Throwable $e) {
                Log::warning('notifyVehicleChange failed (updateAvailability)', ['car_id' => $car->id ?? null, 'error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Statut du véhicule modifié avec succès!',
                'car' => $updatedCar->only(['id', 'name', 'matricule', 'status', 'availability_type']),
            ]);
        } catch (Throwable $e) {
            Log::error('Availability update failed', ['car_id' => $id, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut',
            ], 500);
        }
    }

    // Change car status via PATCH (legacy - use updateAvailability instead)
    public function updateStatus(Request $request, $id)
    {
        return $this->updateAvailability($request, $id);
    }

    // Delete a car
    public function destroy(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        if ($car->is_core) {
            $message = 'Ce véhicule fait partie de la flotte officielle et ne peut pas être supprimé.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            
            return back()->withErrors([
                'car' => $message,
            ]);
        }

        try {
            $carId = $car->id;
            $vehicleName = $car->name;
            
            DB::transaction(function () use ($car, $carId) {
                $car->delete();
                
                // Broadcast deletion to all users for real-time sync
                try {
                    broadcast(new \App\Events\CarDeleted($carId, $car->name))->toOthers();
                } catch (Throwable $e) {
                    Log::debug('Broadcasting not available: ' . $e->getMessage());
                }
            });
            
            $this->notifyVehicleChange('supprime', $vehicleName);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Véhicule supprimé avec succès!',
                ]);
            }

            return redirect()->route('cars.index')->with('success', 'Véhicule supprimé avec succès!');
        } catch (Throwable $e) {
            Log::error('Car deletion failed', ['car_id' => $id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression du véhicule',
                ], 500);
            }
            
            return back()->with('error', 'Erreur lors de la suppression du véhicule');
        }
    }

    private function notifyVehicleChange(string $action, string $vehicleName): void
    {
        $users = User::role(['admin', 'employee', 'super_admin'])->get();

        foreach ($users as $user) {
            try {
                $user->notify(new VehicleChangedNotification($action, $vehicleName));
            } catch (Throwable $e) {
                Log::warning('Vehicle notification email failed; storing database notification only.', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                Notification::sendNow($user, new VehicleChangedNotification($action, $vehicleName), ['database']);
            }
        }
    }
}
