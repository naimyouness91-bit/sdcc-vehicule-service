<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DataEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|super_admin');
    }

    /**
     * Store new vehicle
     */
    public function storeVehicle(Request $request)
    {
        if (!$request->expectsJson()) {
            abort(400);
        }

        try {
            $validated = $request->validate([
                'name'              => 'required|string|max:255',
                'matricule'         => 'required|string|unique:cars,matricule',
                'model'             => 'required|string|max:255',
                'year'              => 'required|integer|min:2000|max:' . date('Y'),
                'km'                => 'required|integer|min:0',
                'status'            => 'required|in:disponible,maintenance',
                'availability_type' => 'required|in:both,weekend,weekday',
            ], [
                'name.required'              => 'Le nom est requis',
                'matricule.required'         => 'La matricule est requise',
                'matricule.unique'           => 'Cette matricule existe déjà',
                'model.required'             => 'Le type/modèle est requis',
                'year.required'              => 'L\'année est requise',
                'km.required'                => 'Le kilométrage est requis',
                'status.required'            => 'Le statut est requis',
                'availability_type.required' => 'Le type de disponibilité est requis',
                'availability_type.in'       => 'Type de disponibilité invalide (both, weekend ou weekday)',
            ]);

            // Create vehicle
            $car = Car::create([
                'name'              => $validated['name'],
                'matricule'         => $validated['matricule'],
                'model'             => $validated['model'],
                'year'              => $validated['year'],
                'km'                => $validated['km'],
                'status'            => $validated['status'],
                'availability_type' => $validated['availability_type'],
                'is_core'           => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Véhicule ajouté avec succès',
                'data' => [
                    'id' => $car->id,
                    'name' => $car->name,
                    'matricule' => $car->matricule,
                    'model' => $car->model,
                    'year' => $car->year,
                    'km' => $car->km,
                    'status' => $car->status,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Vehicle creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du véhicule'
            ], 500);
        }
    }

    /**
     * Update vehicle mileage
     */
    public function storeKilometrage(Request $request)
    {
        if (!$request->expectsJson()) {
            abort(400);
        }

        try {
            $validated = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'new_km' => 'required|integer|min:0',
                'update_date' => 'required|date',
                'user_id' => 'nullable|exists:users,id',
                'reason' => 'nullable|string|max:500',
            ], [
                'car_id.required' => 'Le véhicule est requis',
                'car_id.exists' => 'Véhicule non trouvé',
                'new_km.required' => 'Le kilométrage est requis',
                'update_date.required' => 'La date est requise',
            ]);

            $car = Car::findOrFail($validated['car_id']);
            $oldKm = $car->km;

            // Update vehicle mileage
            $car->update(['km' => $validated['new_km']]);

            // Create record in demandes table with special type
            if ($validated['user_id']) {
                Demande::create([
                    'user_id' => $validated['user_id'],
                    'car_id' => $car->id,
                    'destination' => 'Mise à jour kilométrage',
                    'start_date' => $validated['update_date'],
                    'start_time' => now()->format('H:i'),
                    'end_date' => $validated['update_date'],
                    'kilometers' => $validated['new_km'] - $oldKm,
                    'reason' => $validated['reason'] ?? 'Mise à jour automatique',
                    'status' => 'approved', // Mark as approved automatically
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kilométrage mis à jour avec succès',
                'data' => [
                    'car_id' => $car->id,
                    'old_km' => $oldKm,
                    'new_km' => $validated['new_km'],
                    'difference' => $validated['new_km'] - $oldKm,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Kilometrage update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du kilométrage'
            ], 500);
        }
    }

    /**
     * Get all employees data
     */
    /**
     * Get all vehicles data
     */
    public function getVehicles()
    {
        $vehicles = Car::orderByDesc('created_at')
            ->get()
            ->map(function ($car) {
                return [
                    'id' => $car->id,
                    'name' => $car->name,
                    'matricule' => $car->matricule,
                    'model' => $car->model,
                    'year' => $car->year,
                    'km' => $car->km,
                    'status' => $car->status,
                    'created_at' => $car->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json($vehicles);
    }

    /**
     * Get kilometrage history
     */
    public function getKilometrage()
    {
        $history = Demande::where('destination', 'Mise à jour kilométrage')
            ->with(['car', 'user'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'vehicle' => $record->car?->name . ' (' . $record->car?->matricule . ')',
                    'employee' => $record->user?->name ?? 'N/A',
                    'service' => $record->user?->service ?? 'N/A',
                    'km_change' => $record->kilometers,
                    'reason' => $record->reason,
                    'date' => $record->start_date->format('d/m/Y H:i'),
                ];
            });

        return response()->json($history);
    }

    /**
     * Get demandes/requests data (JSON)
     * Supports optional query params: search, status, service, page, per_page
     */
    public function getRequests(Request $request)
    {
        $query = Demande::with(['car', 'user'])->orderByDesc('created_at');

        // Filters
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('car', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")->orWhere('matricule', 'like', "%{$search}%");
                })
                ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($service = $request->query('service')) {
            $query->whereHas('user', function ($uq) use ($service) {
                $uq->where('service', $service);
            });
        }

        // Pagination support: server-side pagination enabled by default (25 items/page)
        // Pass `per_page=0` to request the full list (not recommended for large datasets).
        $perPage = (int) $request->query('per_page', 25);
        $page = (int) $request->query('page', 1);

        if ($perPage > 0) {
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            $data = $paginator->getCollection()->transform(function ($d) {
                return [
                    'id' => $d->id,
                    'employee' => $d->user?->name ?? 'N/A',
                    'service' => $d->user?->service ?? 'N/A',
                    'vehicle' => $d->car?->name ? ($d->car->name . ' (' . $d->car->matricule . ')') : null,
                    'destination' => $d->destination,
                    'start_date' => $d->start_date?->format('d/m/Y') ?? null,
                    'end_date' => $d->end_date?->format('d/m/Y') ?? null,
                    'start_time' => $d->start_time,
                    'end_time' => $d->end_time ?? null,
                    'kilometers' => $d->kilometers,
                    'reason' => $d->reason,
                    'status' => $d->status,
                    'has_conflict' => (bool) $d->has_conflict,
                    'created_at' => $d->created_at->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ]
            ]);
        }

        // per_page == 0 -> return full list but still provide meta for client compatibility
        $results = $query->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'employee' => $d->user?->name ?? 'N/A',
                'service' => $d->user?->service ?? 'N/A',
                'vehicle' => $d->car?->name ? ($d->car->name . ' (' . $d->car->matricule . ')') : null,
                'destination' => $d->destination,
                'start_date' => $d->start_date?->format('d/m/Y') ?? null,
                'end_date' => $d->end_date?->format('d/m/Y') ?? null,
                'start_time' => $d->start_time,
                'end_time' => $d->end_time ?? null,
                'kilometers' => $d->kilometers,
                'reason' => $d->reason,
                'status' => $d->status,
                'created_at' => $d->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'data' => $results,
            'meta' => [
                'total' => $results->count(),
                'per_page' => $results->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]
        ]);
    }

    /**
     * Get reservations (demande) data for DataEntry UI (JSON, paginated)
     */
    public function getReservations(Request $request)
    {
        $query = Demande::with(['car', 'user'])->orderByDesc('created_at');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('car', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")->orWhere('matricule', 'like', "%{$search}%");
                })
                ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($service = $request->query('service')) {
            $query->whereHas('user', function ($uq) use ($service) {
                $uq->where('service', $service);
            });
        }

        $perPage = (int) $request->query('per_page', 25);
        $page = (int) $request->query('page', 1);

        if ($perPage > 0) {
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            $data = $paginator->getCollection()->transform(function ($d) {
                return [
                    'id' => $d->id,
                    'employee' => $d->user?->name ?? 'N/A',
                    'service' => $d->user?->service ?? 'N/A',
                    'vehicle' => $d->car?->name ? ($d->car->name . ' (' . $d->car->matricule . ')') : null,
                    'destination' => $d->destination,
                    'start_date' => $d->start_date?->format('d/m/Y') ?? null,
                    'end_date' => $d->end_date?->format('d/m/Y') ?? null,
                    'kilometers' => $d->kilometers,
                    'reason' => $d->reason,
                    'status' => $d->status,
                    'has_conflict' => (bool) $d->has_conflict,
                    'created_at' => $d->created_at->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ]
            ]);
        }

        $results = $query->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'employee' => $d->user?->name ?? 'N/A',
                'service' => $d->user?->service ?? 'N/A',
                'vehicle' => $d->car?->name ? ($d->car->name . ' (' . $d->car->matricule . ')') : null,
                'destination' => $d->destination,
                'start_date' => $d->start_date?->format('d/m/Y') ?? null,
                'end_date' => $d->end_date?->format('d/m/Y') ?? null,
                'kilometers' => $d->kilometers,
                'reason' => $d->reason,
                'status' => $d->status,
                'created_at' => $d->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'data' => $results,
            'meta' => [
                'total' => $results->count(),
                'per_page' => $results->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]
        ]);
    }

    /**
     * Delete vehicle
     */
    public function deleteVehicle($id)
    {
        try {
            Car::findOrFail($id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Véhicule supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    /**
     * Update employee
     */
    /**
     * Update vehicle
     */
    public function updateVehicle(Request $request, $id)
    {
        if (!$request->expectsJson()) {
            abort(400);
        }

        try {
            $validated = $request->validate([
                'name'              => 'required|string|max:255',
                'matricule'         => 'required|string|unique:cars,matricule,' . $id,
                'model'             => 'required|string|max:255',
                'year'              => 'required|integer|min:2000',
                'km'                => 'required|integer|min:0',
                'status'            => 'required|in:disponible,maintenance',
                'availability_type' => 'required|in:both,weekend,weekday',
            ]);

            $car = Car::findOrFail($id);
            $car->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Véhicule mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }
}
