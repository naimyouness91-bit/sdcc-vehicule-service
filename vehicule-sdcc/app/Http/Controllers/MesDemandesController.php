<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Car;
use App\Models\User;
use App\Notifications\RequestSubmittedNotification;
use App\Notifications\RequestStatusUpdatedNotification;
use App\Notifications\VehicleReservationNotification;
use App\Services\DemandService;
use App\Services\HrExcelSyncService;
use App\Services\NotificationService;
use App\Services\OptionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Auth;
use Throwable;

class MesDemandesController extends Controller
{
    // Weekend-only day constraints (Friday start → Monday end)
    // Note: No fixed times enforced - users can choose any time for their reservation
    private const WEEKEND_ONLY_DEPARTURE_DAY = 5; // Friday (ISO 8601: Mon=1, Fri=5, Sat=6, Sun=7)
    private const WEEKEND_ONLY_RETURN_DAY = 1; // Monday (ISO 8601)

    public function __construct(
        private readonly HrExcelSyncService $hrExcelSyncService,
        private readonly OptionsService $optionsService,
        private readonly NotificationService $notificationService,
        private readonly DemandService $demandService,
    ) {
    }

    /**
     * Validate weekend-only reservation rules - Only enforces day restrictions
     * 
     * @param Carbon $startDate Departure date
     * @param Carbon $endDate Return date
     * @return array|null Array with error details, or null if valid
     */
    private function validateWeekendOnlyConstraints(Carbon $startDate, Carbon $endDate): ?array
    {
        // Normalize dates to application timezone and to start of day to avoid timezone shifts
        $tz = config('app.timezone') ?? 'UTC';
        $startDateLocal = $startDate->copy()->setTimezone($tz)->startOfDay();
        $endDateLocal = $endDate->copy()->setTimezone($tz)->startOfDay();

        $startDayOfWeek = $startDateLocal->dayOfWeek; // 0=Sun, 1=Mon, 5=Fri, 6=Sat
        $endDayOfWeek = $endDateLocal->dayOfWeek;

        // ===== VALIDATION: START DATE MUST BE FRIDAY =====
        if ($startDayOfWeek !== self::WEEKEND_ONLY_DEPARTURE_DAY) {
            $dayName = ucfirst($startDateLocal->locale('fr')->isoFormat('dddd'));
            return [
                'field' => 'start_date',
                'message' => "Ce véhicule ne peut être réservé que le vendredi. Vous avez sélectionné un {$dayName}.",
            ];
        }

        // ===== VALIDATION: END DATE MUST BE MONDAY =====
        if ($endDayOfWeek !== self::WEEKEND_ONLY_RETURN_DAY) {
            $dayName = ucfirst($endDateLocal->locale('fr')->isoFormat('dddd'));
            return [
                'field' => 'end_date',
                'message' => "La restitution doit se faire le lundi. Vous avez sélectionné un {$dayName}.",
            ];
        }

        return null; // All validations passed - times are not restricted
    }

    public function index()
    {
        $status = request('status');

        // Map French status names to Demande constants
        $statusMap = [
            'en_attente' => Demande::STATUS_PENDING,
            'approuvee' => Demande::STATUS_APPROVED,
            'annulee' => Demande::STATUS_CANCELLED,
            'rejetee' => Demande::STATUS_REJECTED,
        ];

        // Build query with user filter
        $demandesQuery = Demande::query()
            ->where('user_id', Auth::id())
            ->latest();

        // Apply status filter if provided
        if ($status && isset($statusMap[$status])) {
            $demandesQuery->where('status', $statusMap[$status]);
        }

        $demandes = $demandesQuery->get();

        // Compute counts for the user's demandes (unfiltered totals)
        $totalCount = Demande::where('user_id', Auth::id())->count();
        $pendingCount = Demande::where('user_id', Auth::id())->where('status', Demande::STATUS_PENDING)->count();
        $approvedCount = Demande::where('user_id', Auth::id())->where('status', Demande::STATUS_APPROVED)->count();
        $rejectedCount = Demande::where('user_id', Auth::id())->where('status', Demande::STATUS_REJECTED)->count();
        $cancelledCount = Demande::where('user_id', Auth::id())->where('status', Demande::STATUS_CANCELLED)->count();

        return view('mes-demandes.index', [
            'demandes' => $demandes,
            'user' => Auth::user(),
            'statusFilter' => $status,
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'cancelledCount' => $cancelledCount,
        ]);
    }

    public function create(Request $request)
    {
        $carId = $request->query('car_id');
        $startDate = $request->query('start_date');
        
        // Get user's zone
        $user = Auth::user();
        $zone = $user->getZone();
        
        // Get available vehicles for this user's zone
        $availableVehicles = collect();
        if ($zone) {
            // Try to get vehicles from zone relationship
            // Exclude 'unavailable' vehicles; show both 'both' and 'weekend' types
            $availableVehicles = $zone->cars()
                ->where('status', 'disponible')
                ->whereIn('availability_type', ['both', 'weekend'])
                ->orderBy('name')
                ->get();
        }
        
        // Fallback: if no zone (or zone has no cars), show all available vehicles.
        if ($availableVehicles->isEmpty()) {
            $availableVehicles = Car::query()
                ->where('status', 'disponible')
                ->whereIn('availability_type', ['both', 'weekend'])
                ->orderBy('name')
                ->get();
        }
        $employees = [];
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            $employees = User::orderBy('name')->get();
        }
        
        return view('mes-demandes.create', [
            'user' => $user,
            'carId' => $carId,
            'startDate' => $startDate,
            'availableVehicles' => $availableVehicles,
            'userZone' => $zone,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $sender = Auth::user();

        // Build validation rules based on user role
        $rules = [
            'destination' => 'required|string|max:255',
            // custom_destination is nullable (not needed when using predefined destination)
            // but required if destination is '__other__'
            'custom_destination' => 'nullable|required_if:destination,__other__|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'kilometers' => 'nullable|integer|min:0',
            'distance_travelled' => 'nullable|integer|min:0',
            'reason' => 'required|string|max:2000',
            'car_id' => 'required|integer|exists:cars,id',
        ];

        // For admin/super_admin, employee_id is required
        if ($sender->hasAnyRole(['admin', 'super_admin'])) {
            // Allow admins to optionally set a target user via either 'employee_id' or 'user_id'
            $rules['employee_id'] = 'nullable|integer|exists:users,id';
            $rules['user_id'] = 'nullable|integer|exists:users,id';
            // Allow admins to set initial status when creating on behalf
            $rules['status'] = 'nullable|in:pending,approved,rejected,cancelled';
        } else {
            // For employees, allow but ignore user_id/employee_id for authorization check
            $rules['employee_id'] = 'nullable|integer|exists:users,id';
            $rules['user_id'] = 'nullable|integer|exists:users,id';
        }

        // Use Validator so we can return JSON validation errors in testing
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->expectsJson() || app()->environment('testing')) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withInput()->withErrors($validator->errors());
        }

        $validated = $validator->validated();

        // Prevent employees from creating demandes for other users (they must only create for themselves)
        // Check both employee_id and user_id parameters


        if (!$sender->hasAnyRole(['admin', 'super_admin'])) {
            $targetFromRequest = $request->input('employee_id') ?? $request->input('user_id') ?? null;
            if ($targetFromRequest && (int)$targetFromRequest !== $sender->id) {
                if ($request->expectsJson() || app()->environment('testing')) {
                    return response()->json(['error' => 'Not authorized to create demande for other users.'], 403);
                }
                abort(403, 'Not authorized to create demande for other users.');
            }
        }
        
        // Determine the target user (use employee_id or user_id if provided)
        $targetUserId = $sender->id;
        if ($sender->hasAnyRole(['admin', 'super_admin'])) {
            if (!empty($validated['employee_id'])) {
                $targetUserId = (int) $validated['employee_id'];
            } elseif (!empty($validated['user_id'])) {
                $targetUserId = (int) $validated['user_id'];
            }
        }

        // ========== CHECK FOR OVERLAPPING RESERVATIONS ==========
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $hasConflict = false;
        $conflictPeriod = null;

        $conflictingReservation = $this->demandService->findOverlappingReservation(
            (int) $validated['car_id'],
            $startDate,
            $endDate
        );

        if ($conflictingReservation) {
            $conflictStart = $conflictingReservation->start_date->format('d/m/Y');
            $conflictEnd = $conflictingReservation->end_date->format('d/m/Y');
            $conflictPeriod = "du {$conflictStart} au {$conflictEnd}";

            // Admins creating reservations directly still get blocked.
            if ($sender->hasAnyRole(['admin', 'super_admin'])) {
                $errors = ['start_date' => "Ce véhicule est déjà réservé {$conflictPeriod}. Veuillez choisir un autre véhicule ou une autre date."];
                if ($request->expectsJson() || app()->environment('testing')) {
                    return response()->json(['errors' => $errors], 422);
                }
                return back()->withInput()->withErrors($errors);
            }

            $hasConflict = true;
            Log::info('Reservation conflict detected — demande will be saved for admin review', [
                'user_id' => $sender->id,
                'car_id' => $validated['car_id'],
                'conflicting_demande_id' => $conflictingReservation->id,
                'period' => $conflictPeriod,
            ]);
        }
        // ====================================================

        // Check if vehicle status is available
        $car = Car::query()->findOrFail((int) $validated['car_id']);
        if ($car->status !== 'disponible') {
            $errors = ['car_id' => 'Ce véhicule n\'est pas disponible.'];
                    if ($request->expectsJson() || app()->environment('testing')) {
                        return response()->json(['errors' => $errors], 422);
                    }
                    return back()->withInput()->withErrors($errors);
        }
        
        // ========== ZONE VALIDATION ==========
        // If the employee has a zone, enforce zone-based access.
        // If no zone is assigned, keep a permissive fallback so urgent requests remain possible.
            if ($sender->isEmployee()) {
                if ($sender->getZone() && !$sender->canAccessVehicle($car)) {
                    $errors = ['car_id' => 'Ce véhicule n\'est pas disponible pour votre zone de planification. Contactez l\'administrateur si vous pensez que c\'est une erreur.'];
                    if (app()->environment('testing')) {
                        return response()->json(['errors' => $errors], 422);
                    }
                    return back()->withInput()->withErrors($errors);
                }
            }
        // ======================================
        
        // Check if vehicle is unavailable
        $availabilityType = $car->availability_type ?? 'both';
        if ($availabilityType === 'unavailable') {
            $errors = ['car_id' => 'Ce véhicule est indisponible et ne peut pas être réservé.'];
            if ($request->expectsJson() || app()->environment('testing')) {
                return response()->json(['errors' => $errors], 422);
            }
            return back()->withInput()->withErrors($errors);
        }
        
        // ===== VALIDATE WEEKEND-ONLY CONSTRAINTS =====
        // Weekend-only vehicles: Must start Friday and end Monday (any times allowed)
        if ((string) $availabilityType === 'weekend') {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

            $validationError = $this->validateWeekendOnlyConstraints($startDate, $endDate);
            if ($validationError) {
                $errors = [$validationError['field'] => $validationError['message']];
                if ($request->expectsJson() || app()->environment('testing')) {
                    return response()->json(['errors' => $errors], 422);
                }
                return back()->withInput()->withErrors($errors);
            }
        }
        // =========================================

        // Determine final destination value: if '__other__' was selected, use the provided custom_destination
        $finalDestination = $validated['destination'] ?? null;
        if (isset($validated['destination']) && $validated['destination'] === '__other__') {
            $finalDestination = $validated['custom_destination'] ?? null;
        }

        // Create demande for the target user (admin creating on behalf, or employee for themselves)
        $demande = Demande::create([
            'user_id' => $targetUserId,
            'car_id' => (int) $validated['car_id'],
            'destination' => $finalDestination,
            'start_date' => $validated['start_date'],
            'start_time' => $validated['start_time'],
            'end_date' => $validated['end_date'],
            'end_time' => $validated['end_time'] ?? null,
            'kilometers' => $validated['kilometers'] ?? null,
            'distance_travelled' => $validated['distance_travelled'] ?? ($validated['kilometers'] ?? null),
            'reason' => $validated['reason'],
            'status' => ($sender->hasAnyRole(['admin', 'super_admin']) && !empty($validated['status'])) ? $validated['status'] : Demande::STATUS_PENDING,
            'has_conflict' => $hasConflict,
        ]);

        // Safety: if a non-admin somehow created a demande for another user, undo and forbid.
        if (!$sender->hasAnyRole(['admin', 'super_admin']) && $demande->user_id !== $sender->id) {
            try {
                $demande->delete();
            } catch (Throwable $e) {
                // ignore delete errors
            }
            if ($request->expectsJson() || app()->environment('testing')) {
                return response()->json(['error' => 'Not authorized to create demande for other users.'], 403);
            }
            abort(403, 'Not authorized to create demande for other users.');
        }

        // Notify the target employee about the reservation
        $targetEmployee = User::find($targetUserId);
        if ($targetEmployee) {
            $this->notificationService->notifyUser(
                $targetEmployee,
                    new VehicleReservationNotification(
                        destination: $finalDestination,
                        carId: isset($validated['car_id']) ? (int) $validated['car_id'] : null,
                        demandeId: $demande->id
                    ),
                async: true
            );
        }

        // Notify admins about new request submission (only if sender is not admin)
        if (!$sender->hasAnyRole(['admin', 'super_admin'])) {
            $this->notificationService->notifyAllAdmins(
                new RequestSubmittedNotification(
                    employeeName: $sender->name,
                    destination: $finalDestination,
                    carId: isset($validated['car_id']) ? (int) $validated['car_id'] : null,
                    demandeId: $demande->id,
                    hasConflict: $hasConflict,
                    conflictPeriod: $conflictPeriod,
                ),
                async: true
            );
        }

        // If admin created on behalf of another employee, also notify the admin
        if ($sender->hasAnyRole(['admin', 'super_admin']) && $targetUserId !== $sender->id) {
            $this->notificationService->notifyUser(
                $sender,
                new VehicleReservationNotification(
                    destination: $finalDestination,
                    carId: isset($validated['car_id']) ? (int) $validated['car_id'] : null,
                    demandeId: $demande->id
                ),
                async: true
            );
        }

        $this->hrExcelSyncService->recordSubmission($sender, $validated);

        if (app()->environment('testing')) {
            return response()->json(['success' => true, 'demande_id' => $demande->id], 201);
        }

        return redirect()->route('mes-demandes.index')->with('success', 'Votre demande de réservation a été soumise avec succès (ID #' . $demande->id . '). Vous recevrez une notification une fois qu\'elle sera approuvée.');
    }

    public function approve(Request $request, $id)
    {
        $demande = Demande::query()->findOrFail($id);
        $employee = User::findOrFail($demande->user_id);
        $demande->update(['status' => Demande::STATUS_APPROVED]);
        
        // Notify employee about approval with email and in-app notification
        $this->notificationService->notifyUser(
            $employee,
            new RequestStatusUpdatedNotification(
                status: 'approuvee',
                destination: $demande->destination,
                demandeId: $demande->id
            ),
            async: true
        );
        
        $this->hrExcelSyncService->updateRequestStatus($employee->name, $demande->destination, 'approved');

        if (app()->environment('testing')) {
            return response()->json(['success' => true], 200);
        }

        return back()->with('success', 'La demande #' . $id . ' a ete approuvee.');
    }

    public function reject(Request $request, $id)
    {
        $demande = Demande::query()->findOrFail($id);
        $employee = User::findOrFail($demande->user_id);
        $demande->update(['status' => Demande::STATUS_REJECTED]);
        
        // Notify employee about rejection with email and in-app notification
        $this->notificationService->notifyUser(
            $employee,
            new RequestStatusUpdatedNotification(
                status: 'rejetee',
                destination: $demande->destination,
                demandeId: $demande->id
            ),
            async: true
        );
        
        $this->hrExcelSyncService->updateRequestStatus($employee->name, $demande->destination, 'rejected');

        if (app()->environment('testing')) {
            return response()->json(['success' => true], 200);
        }

        return back()->with('success', 'La demande #' . $id . ' a ete rejetee.');
    }

    public function show($id)
    {
        $query = Demande::query()->with('user');

        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            $query->where('user_id', Auth::id());
        }

        $demande = $query->findOrFail($id);

        return view('mes-demandes.show', [
            'demande' => $demande,
            'user' => Auth::user()
        ]);
    }

    /**
     * Update an existing demande (only pending demandes can be updated).
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $demande = Demande::findOrFail($id);

        // Only allow owner or admins to update
        if ($demande->user_id !== $user->id && !$user->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Not authorized to update this demande.');
        }

        // Prevent updates on non-pending demandes
        if ($demande->status !== Demande::STATUS_PENDING) {
            abort(403, 'Cannot modify a demande that is not pending.');
        }

        $validated = $request->validate([
            'destination' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'reason' => 'sometimes|required|string|max:2000',
        ]);

        $demande->update($validated);

        return response()->json(['success' => true, 'message' => 'Demande mise à jour.']);
    }

    public function suggestDestinations(Request $request)
    {
        $query = $request->query('q', '');
        
        // Predefined list of destinations (cities + service stations)
        $cities = [
            'Casablanca',
            'Rabat',
            'Marrakech',
            'Jorf Lasfar',
            'Station-service Kénitra',
            'Station-service Sidi Kacem',
            'Station-service ADM Oualidia',
            'Station-service Aït Ourir',
            'Station-service Sfassif',
            'Station-service Oued Laabid',
            'Station-service Guisser',
            'Station-service Toualaa',
            'Station-service Bouskoura',
            'Station-service Aït Malek',
            'Station-service Agadir',
        ];

        $searchLower = strtolower($query);
        
        // Filter cities with fuzzy matching
        $suggestions = array_values(array_filter($cities, function ($city) use ($searchLower, $query) {
            $cityLower = strtolower($city);
            
            // Exact prefix match (highest priority)
            if (strpos($cityLower, $searchLower) === 0) {
                return true;
            }
            
            // Fuzzy match - contains the query
            if (strpos($cityLower, $searchLower) !== false) {
                return true;
            }
            
            // Levenshtein distance for typos (up to 2 character difference)
            if (strlen($query) >= 3 && levenshtein($searchLower, $cityLower) <= 2) {
                return true;
            }
            
            return false;
        }));

        // Sort by relevance (starts with query first)
        usort($suggestions, function ($a, $b) use ($searchLower) {
            $aMatch = strpos(strtolower($a), $searchLower) === 0 ? 1 : 0;
            $bMatch = strpos(strtolower($b), $searchLower) === 0 ? 1 : 0;
            return $bMatch - $aMatch;
        });

        return response()->json([
            'suggestions' => array_slice($suggestions, 0, 10),
        ]);
    }

    /**
     * Display personal reservation history with filters
     */
    public function history(Request $request)
    {
        $filters = $this->ownHistoryFiltersFromRequest($request);
        $statusFilter = $filters['statusFilter'];
        $cardFilter = $this->resolveOwnHistoryCardFilter($statusFilter);

        $baseQuery = $this->buildOwnHistoryQuery($filters);
        $stats = $this->ownHistoryStatsFromQuery(clone $baseQuery);

        $reservationsQuery = clone $baseQuery;
        if ($statusFilter !== 'all') {
            $reservationsQuery->where('status', $statusFilter);
        }
        $reservations = $reservationsQuery->get();

        $cars = Car::query()
            ->orderBy('name')
            ->get(['id', 'name', 'matricule']);

        return view('mes-demandes.history', [
            'reservations' => $reservations,
            'cars' => $cars,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'cardFilter' => $cardFilter,
            'carFilter' => $filters['carFilter'],
            'dateFrom' => $filters['dateFrom'],
            'dateTo' => $filters['dateTo'],
            'search' => $filters['search'],
            'user' => Auth::user(),
        ]);
    }

    /**
     * Filter personal reservation history by stat card (AJAX / JSON).
     */
    public function filterHistory(Request $request)
    {
        $filters = $this->ownHistoryFiltersFromRequest($request);
        $cardFilter = (string) $request->query('card', 'total');
        $statusFilter = $this->ownHistoryStatusFromCard($cardFilter);

        $baseQuery = $this->buildOwnHistoryQuery($filters);
        $stats = $this->ownHistoryStatsFromQuery(clone $baseQuery);

        $reservationsQuery = clone $baseQuery;
        if ($statusFilter !== null) {
            $reservationsQuery->where('status', $statusFilter);
        }
        $reservations = $reservationsQuery->get();

        $html = view('mes-demandes.partials.history-table-body', [
            'reservations' => $reservations,
        ])->render();

        return response()->json([
            'html' => $html,
            'count' => $reservations->count(),
            'card' => $cardFilter,
            'stats' => $stats,
            'status_filter' => $statusFilter ?? 'all',
        ]);
    }

    /**
     * @return array{statusFilter: string, carFilter: int, dateFrom: ?string, dateTo: ?string, search: string}
     */
    private function ownHistoryFiltersFromRequest(Request $request): array
    {
        $statusFilter = (string) $request->query('status', 'all');
        $card = (string) $request->query('card', '');

        if ($card !== '') {
            $mapped = $this->ownHistoryStatusFromCard($card);
            $statusFilter = $mapped ?? 'all';
        } elseif ($statusFilter !== 'all') {
            $statusFilter = $this->normalizeOwnHistoryStatusFilter($statusFilter) ?? 'all';
        }

        return [
            'statusFilter' => $statusFilter,
            'carFilter' => (int) $request->query('car_id', 0),
            'dateFrom' => $request->query('date_from'),
            'dateTo' => $request->query('date_to'),
            'search' => trim((string) $request->query('q', '')),
        ];
    }

    private function buildOwnHistoryQuery(array $filters)
    {
        $query = Demande::query()
            ->where('user_id', Auth::id())
            ->with(['car:id,name,matricule,status'])
            ->orderByDesc('created_at');

        if ($filters['carFilter'] > 0) {
            $query->where('car_id', $filters['carFilter']);
        }

        if ($filters['dateFrom']) {
            $query->where('start_date', '>=', $filters['dateFrom']);
        }

        if ($filters['dateTo']) {
            $query->where('end_date', '<=', $filters['dateTo']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('car', function ($carQ) use ($search) {
                    $carQ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%');
                })
                    ->orWhere('destination', 'like', '%' . $search . '%')
                    ->orWhere('reason', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    /**
     * @return array{total: int, pending: int, approved: int, cancelled: int}
     */
    private function ownHistoryStatsFromQuery($query): array
    {
        $collection = $query->get();

        return [
            'total' => $collection->count(),
            'pending' => $collection->where('status', Demande::STATUS_PENDING)->count(),
            'approved' => $collection->where('status', Demande::STATUS_APPROVED)->count(),
            'cancelled' => $collection->where('status', Demande::STATUS_CANCELLED)->count(),
        ];
    }

    private function ownHistoryStatusFromCard(string $card): ?string
    {
        return match ($card) {
            'total', 'all' => null,
            'en_attente', 'pending' => Demande::STATUS_PENDING,
            'approuvee', 'approved' => Demande::STATUS_APPROVED,
            'annulee', 'cancelled' => Demande::STATUS_CANCELLED,
            'rejetee', 'rejected' => Demande::STATUS_REJECTED,
            default => null,
        };
    }

    private function normalizeOwnHistoryStatusFilter(string $status): ?string
    {
        return match ($status) {
            'all', 'total' => null,
            'en_attente', 'pending' => Demande::STATUS_PENDING,
            'approuvee', 'approved' => Demande::STATUS_APPROVED,
            'annulee', 'cancelled' => Demande::STATUS_CANCELLED,
            'rejetee', 'rejected' => Demande::STATUS_REJECTED,
            default => in_array($status, [
                Demande::STATUS_PENDING,
                Demande::STATUS_APPROVED,
                Demande::STATUS_CANCELLED,
                Demande::STATUS_REJECTED,
            ], true) ? $status : null,
        };
    }

    private function resolveOwnHistoryCardFilter(string $statusFilter): string
    {
        return match ($statusFilter) {
            Demande::STATUS_PENDING => 'en_attente',
            Demande::STATUS_APPROVED => 'approuvee',
            Demande::STATUS_CANCELLED => 'annulee',
            Demande::STATUS_REJECTED => 'rejetee',
            default => 'total',
        };
    }

    /**
     * Export personal reservation history as CSV
     */
    public function exportHistory(Request $request)
    {
        $statusFilter = (string) $request->query('status', 'all');
        $carFilter = (int) $request->query('car_id', 0);
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('q', ''));

        $query = Demande::query()
            ->where('user_id', Auth::id())
            ->with(['car:id,name,matricule,status'])
            ->orderByDesc('created_at');

        // Apply filters (same as history view)
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if ($carFilter > 0) {
            $query->where('car_id', $carFilter);
        }
        if ($dateFrom) {
            $query->where('start_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('end_date', '<=', $dateTo);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('car', function ($carQ) use ($search) {
                    $carQ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%');
                })
                ->orWhere('destination', 'like', '%' . $search . '%')
                ->orWhere('reason', 'like', '%' . $search . '%');
            });
        }

        $reservations = $query->get();
        $user = Auth::user();

        // Create CSV content
        $csvHeaders = [
            'N°',
            'Date de Demande',
            'Véhicule',
            'Matricule',
            'Destination',
            'Date de Départ',
            'Date de Retour',
            'Raison',
            'Statut',
        ];

        $csvData = [];
        foreach ($reservations as $index => $reservation) {
            $csvData[] = [
                $index + 1,
                $reservation->created_at->format('d/m/Y H:i'),
                $reservation->car?->name ?? 'N/A',
                $reservation->car?->matricule ?? 'N/A',
                $reservation->destination ?? 'N/A',
                $reservation->start_date->format('d/m/Y'),
                $reservation->end_date->format('d/m/Y'),
                $reservation->reason ?? 'N/A',
                ucfirst($reservation->status),
            ];
        }

        $delimiter = ';';

        // Generate CSV content with proper formatting
        $csvLines = [];
        
        // Add headers with proper quoting
        $csvLines[] = $this->escapeCsvLine($csvHeaders, $delimiter);
        
        // Add data rows
        foreach ($csvData as $row) {
            $csvLines[] = $this->escapeCsvLine($row, $delimiter);
        }
        
        // Add UTF-8 BOM for Excel and use CRLF line endings
        $csvContent = "\xEF\xBB\xBF" . implode("\r\n", $csvLines) . "\r\n";
        
        $filename = 'mon_historique_reservations_' . Carbon::now()->format('Ymd_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Escape and format a CSV line properly according to RFC 4180
     * 
     * @param array $fields
     * @param string $delimiter
     * @return string
     */
    private function escapeCsvLine(array $fields, string $delimiter = ';'): string
    {
        $escaped = array_map(function($field) use ($delimiter) {
            // Convert value to string if necessary
            $field = (string) $field;
            
            // If field contains delimiter, quote, or newline, wrap in quotes and escape inner quotes
            if (
                strpos($field, $delimiter) !== false ||
                strpos($field, '"') !== false ||
                strpos($field, "\n") !== false ||
                strpos($field, "\r") !== false
            ) {
                return '"' . str_replace('"', '""', $field) . '"';
            }
            
            // Otherwise return as-is
            return $field;
        }, $fields);
        
        return implode($delimiter, $escaped);
    }

    /**
     * Cancel a reservation (Employee can cancel their own, Admin/SuperAdmin can cancel any)
     */
    public function cancelOwn(Request $request, $id)
    {
        $user = Auth::user();

        try {
            $demande = Demande::findOrFail($id);

            // Authorization: Check if user is the owner or has admin role
            if ($demande->user_id !== $user->id && !$user->hasAnyRole(['admin', 'super_admin'])) {
                Log::warning("Unauthorized cancellation attempt", [
                    'user_id' => $user->id,
                    'demande_id' => $id,
                    'demande_owner' => $demande->user_id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à annuler cette demande.',
                ], 403);
            }

            // Check if status is pending (can only cancel pending requests)
            if ($demande->status !== Demande::STATUS_PENDING) {
                Log::info("Cancellation rejected - not pending", [
                    'demande_id' => $id,
                    'current_status' => $demande->status,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => "Vous ne pouvez annuler que les demandes en attente. Statut actuel : {$demande->status}.",
                ], 422);
            }

            // Update status to cancelled
            $demande->update(['status' => Demande::STATUS_CANCELLED]);

            Log::info("Reservation cancelled successfully", [
                'demande_id' => $id,
                'cancelled_by_user_id' => $user->id,
                'cancelled_by_role' => $user->roles->pluck('name')->join(', '),
                'original_owner' => $demande->user_id,
                'destination' => $demande->destination,
            ]);

            // Notify the employee (if cancelled by admin)
            if ($user->id !== $demande->user_id) {
                $this->notificationService->notifyUser(
                    $demande->user,
                    new RequestStatusUpdatedNotification(
                        status: 'annulee',
                        destination: $demande->destination,
                        demandeId: $demande->id
                    ),
                    async: true
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande annulée avec succès.',
                'data' => [
                    'id' => $demande->id,
                    'status' => $demande->status,
                    'destination' => $demande->destination,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Error cancelling reservation", [
                'demande_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation : ' . $e->getMessage(),
            ], 500);
        }
    }
}

