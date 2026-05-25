<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\User;
use App\Models\Car;
use App\Exports\DemandesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AdminReservationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Allow both admin and super_admin roles
        $this->middleware('role:admin|super_admin');
    }

    /**
     * Display all reservations with filters and search
     */
    public function index(Request $request)
    {
        $statusFilter = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 15);

        $query = Demande::query()
            ->with(['user', 'car'])
            ->orderByDesc('created_at');

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Apply search filter (employee name or car name/matricule)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQ) use ($search) {
                    $userQ->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('car', function ($carQ) use ($search) {
                    $carQ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%');
                })
                ->orWhere('destination', 'like', '%' . $search . '%');
            });
        }

        // Paginate results
        $reservations = $query->paginate($perPage)
            ->appends($request->query());

        // Calculate stats
        $stats = [
            'total' => Demande::count(),
            'pending' => Demande::where('status', Demande::STATUS_PENDING)->count(),
            'approved' => Demande::where('status', Demande::STATUS_APPROVED)->count(),
            'cancelled' => Demande::where('status', Demande::STATUS_CANCELLED)->count(),
        ];

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show form to create a reservation for someone else (Admin only)
     */
    public function create()
    {
        // Get all users (employees)
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        })
        ->orderBy('name')
        ->get(['id', 'name', 'email', 'service']);

        // Get available vehicles
        $cars = Car::query()
            ->where('status', 'disponible')
            ->orderBy('name')
            ->get(['id', 'name', 'matricule', 'status', 'availability_type']);

        return view('admin.reservations.create', [
            'users' => $users,
            'cars' => $cars,
        ]);
    }

    /**
     * Store a new reservation on behalf of a user (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'car_id' => 'required|integer|exists:cars,id',
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'kilometers' => 'nullable|integer|min:0',
            'reason' => 'required|string|max:2000',
            'status' => 'nullable|in:pending,approved',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $car = Car::findOrFail($validated['car_id']);

        \Log::info("Creating reservation for user", [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'car_id' => $car->id,
            'car_name' => $car->name,
        ]);

        // Validate car status
        if ($car->status !== 'disponible') {
            \Log::warning("Car not available", [
                'car_id' => $car->id,
                'status' => $car->status,
            ]);
            return back()->withInput()->withErrors([
                'car_id' => 'Ce véhicule n\'est pas disponible.',
            ]);
        }

        // Check for overlapping reservations
        $conflictingReservation = Demande::query()
            ->where('car_id', $validated['car_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                          ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->first();

        if ($conflictingReservation) {
            $conflictStart = $conflictingReservation->start_date->format('d/m/Y');
            $conflictEnd = $conflictingReservation->end_date->format('d/m/Y');
            
            \Log::warning("Conflict detected", [
                'car_id' => $validated['car_id'],
                'conflicting_reservation_id' => $conflictingReservation->id,
                'dates' => "$conflictStart - $conflictEnd",
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'car_id' => "Ce véhicule est déjà réservé du {$conflictStart} au {$conflictEnd}. Veuillez choisir un autre véhicule ou une autre date.",
                ]);
        }

        // Create the reservation
        $demande = Demande::create([
            'user_id' => $validated['user_id'],
            'car_id' => $validated['car_id'],
            'destination' => $validated['destination'],
            'start_date' => $validated['start_date'],
            'start_time' => $validated['start_time'],
            'end_date' => $validated['end_date'],
            'end_time' => $validated['end_time'] ?? null,
            'kilometers' => $validated['kilometers'] ?? null,
            'distance_travelled' => $validated['kilometers'] ?? null,
            'reason' => $validated['reason'],
            'status' => $validated['status'] ?? Demande::STATUS_PENDING,
        ]);

        \Log::info("Reservation created successfully by admin", [
            'demande_id' => $demande->id,
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => $demande->status,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.reservations.index')
            ->with('success', "Réservation créée avec succès pour {$user->name} (ID #" . $demande->id . ").");
    }

    /**
     * Show reservation details
     */
    public function show($id)
    {
        $reservation = Demande::with(['user', 'car'])->findOrFail($id);

        return view('admin.reservations.show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Show form to edit an existing reservation
     */
    public function edit($id)
    {
        $reservation = Demande::with(['user', 'car'])->findOrFail($id);

        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        })
        ->orderBy('name')
        ->get(['id', 'name', 'email', 'service']);

        $cars = Car::query()
            ->orderBy('name')
            ->get(['id', 'name', 'matricule', 'status', 'availability_type']);

        return view('admin.reservations.edit', [
            'reservation' => $reservation,
            'users'       => $users,
            'cars'        => $cars,
        ]);
    }

    /**
     * Update an existing reservation
     */
    public function update(Request $request, $id)
    {
        $reservation = Demande::findOrFail($id);

        $validated = $request->validate([
            'user_id'     => 'required|integer|exists:users,id',
            'car_id'      => 'required|integer|exists:cars,id',
            'destination' => 'required|string|max:255',
            'start_date'  => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'end_time'    => 'nullable|date_format:H:i',
            'kilometers'  => 'nullable|integer|min:0',
            'reason'      => 'required|string|max:2000',
            'status'      => 'nullable|in:pending,approved,cancelled,rejected',
        ]);

        // Check for overlapping reservations (exclude the current one)
        $conflicting = Demande::query()
            ->where('car_id', $validated['car_id'])
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('start_date', '<=', $validated['start_date'])
                         ->where('end_date', '>=', $validated['end_date']);
                  });
            })
            ->first();

        if ($conflicting) {
            $cs = $conflicting->start_date->format('d/m/Y');
            $ce = $conflicting->end_date->format('d/m/Y');
            return back()->withInput()->withErrors([
                'car_id' => "Ce véhicule est déjà réservé du {$cs} au {$ce}.",
            ]);
        }

        $reservation->update([
            'user_id'           => $validated['user_id'],
            'car_id'            => $validated['car_id'],
            'destination'       => $validated['destination'],
            'start_date'        => $validated['start_date'],
            'start_time'        => $validated['start_time'],
            'end_date'          => $validated['end_date'],
            'end_time'          => $validated['end_time'] ?? null,
            'kilometers'        => $validated['kilometers'] ?? null,
            'distance_travelled'=> $validated['kilometers'] ?? null,
            'reason'            => $validated['reason'],
            'status'            => $validated['status'] ?? $reservation->status,
        ]);

        \Log::info('Admin updated reservation', [
            'reservation_id' => $reservation->id,
            'admin_id'       => auth()->id(),
        ]);

        return redirect()->route('admin.reservations.index')
            ->with('success', "Réservation #" . $reservation->id . " mise à jour avec succès.");
    }

    /**
     * Permanently delete a reservation
     */
    public function destroy($id)
    {
        $reservation = Demande::findOrFail($id);

        \Log::info('Admin deleted reservation', [
            'reservation_id' => $reservation->id,
            'admin_id'       => auth()->id(),
            'destination'    => $reservation->destination,
            'status'         => $reservation->status,
        ]);

        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => "Réservation #{$id} supprimée définitivement.",
        ]);
    }

    /**
     * Approve a reservation
     */
    public function approve(Request $request, $id)
    {
        \Log::info("Approve request received", [
            'id' => $id,
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->roles->pluck('name')->join(', '),
        ]);

        try {
            $demande = Demande::findOrFail($id);
            \Log::info("Demande found", [
                'id' => $demande->id,
                'status' => $demande->status,
                'car_id' => $demande->car_id,
            ]);

            // Check for conflicts with other approved reservations
            $conflict = Demande::where('car_id', $demande->car_id)
                ->where('id', '!=', $demande->id)
                ->where('status', Demande::STATUS_APPROVED)
                ->where(function ($q) use ($demande) {
                    $q->whereBetween('start_date', [$demande->start_date, $demande->end_date])
                        ->orWhereBetween('end_date', [$demande->start_date, $demande->end_date])
                        ->orWhere(function ($q2) use ($demande) {
                            $q2->where('start_date', '<=', $demande->start_date)
                                ->where('end_date', '>=', $demande->end_date);
                        });
                })
                ->first();

            if ($conflict) {
                \Log::warning("Conflict detected", [
                    'conflicting_reservation_id' => $conflict->id,
                    'dates' => "{$conflict->start_date->format('d/m/Y')} - {$conflict->end_date->format('d/m/Y')}",
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => "Ce véhicule est déjà réservé du {$conflict->start_date->format('d/m/Y')} au {$conflict->end_date->format('d/m/Y')}.",
                ], 409);
            }

            $demande->update(['status' => Demande::STATUS_APPROVED]);
            
            \Log::info("Reservation approved successfully", [
                'id' => $demande->id,
                'new_status' => $demande->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réservation approuvée avec succès.',
                'data' => [
                    'id' => $demande->id,
                    'status' => $demande->status,
                ],
            ]);
        } catch (\ModelNotFoundException $e) {
            \Log::error("Demande not found", ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Demande introuvable.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Error approving reservation", [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, $id)
    {
        \Log::info("Cancel request received", [
            'id' => $id,
            'user_id' => auth()->id(),
        ]);

        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:255',
            ]);

            $demande = Demande::findOrFail($id);
            \Log::info("Cancelling demande", [
                'id' => $demande->id,
                'current_status' => $demande->status,
            ]);

            $demande->update([
                'status' => Demande::STATUS_CANCELLED,
            ]);

            \Log::info("Reservation cancelled successfully", [
                'id' => $demande->id,
                'new_status' => $demande->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée avec succès.',
                'data' => [
                    'id' => $demande->id,
                    'status' => $demande->status,
                ],
            ]);
        } catch (\ModelNotFoundException $e) {
            \Log::error("Demande not found for cancel", ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Demande introuvable.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Error cancelling reservation", [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change reservation status
     */
    public function updateStatus(Request $request, $id)
    {
        \Log::info("Update status request received", [
            'id' => $id,
            'new_status' => $request->get('status'),
            'user_id' => auth()->id(),
        ]);

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected,cancelled',
            ]);

            $demande = Demande::findOrFail($id);
            \Log::info("Demande found for status update", [
                'id' => $demande->id,
                'current_status' => $demande->status,
                'new_status' => $validated['status'],
            ]);

            // If approving, check for conflicts
            if ($validated['status'] === Demande::STATUS_APPROVED) {
                $conflict = Demande::where('car_id', $demande->car_id)
                    ->where('id', '!=', $demande->id)
                    ->where('status', Demande::STATUS_APPROVED)
                    ->where(function ($q) use ($demande) {
                        $q->whereBetween('start_date', [$demande->start_date, $demande->end_date])
                            ->orWhereBetween('end_date', [$demande->start_date, $demande->end_date])
                            ->orWhere(function ($q2) use ($demande) {
                                $q2->where('start_date', '<=', $demande->start_date)
                                    ->where('end_date', '>=', $demande->end_date);
                            });
                    })
                    ->first();

                if ($conflict) {
                    \Log::warning("Conflict detected on update", [
                        'reservation_id' => $demande->id,
                        'conflicting_id' => $conflict->id,
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => "Ce véhicule est déjà réservé du {$conflict->start_date->format('d/m/Y')} au {$conflict->end_date->format('d/m/Y')}.",
                    ], 409);
                }
            }

            $demande->update(['status' => $validated['status']]);

            \Log::info("Reservation status updated successfully", [
                'id' => $demande->id,
                'status' => $demande->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès.',
                'data' => [
                    'id' => $demande->id,
                    'status' => $demande->status,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning("Validation error on status update", [
                'id' => $id,
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['status'] ?? []),
                'errors' => $e->errors(),
            ], 422);
        } catch (\ModelNotFoundException $e) {
            \Log::error("Demande not found for status update", ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Demande introuvable.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Error updating reservation status", [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export all reservations to Excel
     */
    public function export(Request $request)
    {
        // Get all demandes with relationships (no pagination)
        $demandes = Demande::query()
            ->with(['user', 'car'])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'requests_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new DemandesExport($demandes),
            $filename
        );
    }

    /**
     * Get real-time statistics for polling (API endpoint)
     */
    public function getStats(Request $request)
    {
        $stats = [
            'total' => Demande::count(),
            'pending' => Demande::where('status', Demande::STATUS_PENDING)->count(),
            'approved' => Demande::where('status', Demande::STATUS_APPROVED)->count(),
            'cancelled' => Demande::where('status', Demande::STATUS_CANCELLED)->count(),
            'latest_id' => Demande::latest('created_at')->first()?->id,
            'timestamp' => now()->getTimestamp(),
        ];

        return response()->json($stats);
    }
}
