<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\Demande;
use App\Models\KilometrageEntry;
use App\Models\PlanningWindow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;

class AdminDataManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|super_admin']);
    }

    /**
     * Load tab content via AJAX
     */
    public function loadTab($tab)
    {
        return match ($tab) {
            'kilometrage' => $this->loadKilomettrageTab(),
            'requests' => $this->loadRequestsTab(),
            'reservations' => $this->loadReservationsTab(),
            'planning-windows' => $this->loadPlanningWindowsTab(),
            'notifications' => $this->loadNotificationsTab(),
            default => '<div class="empty-state"><i class="fas fa-question-circle"></i><p>Onglet non trouvé</p></div>'
        };
    }

    /**
     * Load Kilometrage Tab
     */
    private function loadKilomettrageTab()
    {
        $demandes = Demande::with(['user', 'car'])
            ->whereNotNull('kilometers')
            ->get();

        return view('admin.tables.kilometrage', compact('demandes'))->render();
    }

    /**
     * Load Requests Tab (Pending Demandes)
     */
    private function loadRequestsTab()
    {
        $requests = Demande::with(['user', 'car'])
            ->where('status', 'pending')
            ->get();

        return view('admin.tables.requests', compact('requests'))->render();
    }

    /**
     * Load Reservations Tab
     */
    private function loadReservationsTab()
    {
        $reservations = Demande::with(['user', 'car'])->get();

        return view('admin.tables.reservations', compact('reservations'))->render();
    }

    /**
     * Load Planning Windows Tab
     */
    private function loadPlanningWindowsTab()
    {
        $windows = PlanningWindow::all();

        return view('admin.tables.planning-windows', compact('windows'))->render();
    }

    /**
     * Load Notifications Tab
     */
    private function loadNotificationsTab()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('admin.tables.notifications', compact('notifications'))->render();
    }

    /**
     * Main admin dashboard view
     */
    public function index()
    {
        return view('admin.data.index', [
            'title' => 'Gestion des données',
            'icon' => 'fas fa-database',
        ]);
    }

    public function kilometrage()
    {
        $cars = Car::orderBy('name')->get();
        $demandes = Demande::with(['user', 'car'])->whereNotNull('kilometers')->get();
        $highlightVehicles = Car::orderBy('id')->take(2)->get()->map(function ($car) use ($demandes) {
            $carDemandes = $demandes->where('car_id', $car->id);
            $totalKm = (float) $carDemandes->sum(function ($d) {
                return (float) ($d->kilometers ?? 0);
            });
            $last = $carDemandes->sortByDesc('end_date')->first();

            return [
                'id' => $car->id,
                'name' => $car->name ?? ('Véhicule #' . $car->id),
                'plate' => $car->matricule ?? null,
                'trips' => (int) $carDemandes->count(),
                'total_km' => $totalKm,
                'last_km' => $last?->kilometers,
                'last_date' => $last?->end_date,
            ];
        });

        return view('admin.data.section', [
            'title' => 'Kilométrage',
            'icon' => 'fas fa-tachometer-alt',
            'tableView' => 'admin.tables.kilometrage',
            'demandes' => $demandes,
            'highlightVehicles' => $highlightVehicles,
            'cars' => $cars,
        ]);
    }

    public function kilometrageVehicle(Request $request, Car $car)
    {
        $entries = KilometrageEntry::with('creator')
            ->where('car_id', $car->id)
            ->orderByDesc('recorded_at')
            ->limit(50)
            ->get();

        $demandes = Demande::with('user')
            ->where('car_id', $car->id)
            ->whereNotNull('kilometers')
            ->orderByDesc('end_date')
            ->limit(50)
            ->get();

        // Merge into a single timeline-like array for UI.
        $history = collect()
            ->concat($entries->map(fn ($e) => [
                'type' => 'entry',
                'date' => $e->recorded_at,
                'kilometers' => (float) $e->kilometers,
                'employee' => $e->creator?->name,
                'destination' => null,
                'note' => $e->note,
            ]))
            ->concat($demandes->map(fn ($d) => [
                'type' => 'demande',
                'date' => $d->end_date,
                'kilometers' => (float) ($d->kilometers ?? 0),
                'employee' => $d->user?->name,
                'destination' => $d->destination,
                'note' => $d->reason,
            ]))
            ->sortByDesc('date')
            ->values();

        $stats = [
            'total_km' => (float) $history->sum('kilometers'),
            'records' => (int) $history->count(),
            'last_km' => $history->first()['kilometers'] ?? 0,
            'last_date' => $history->first()['date'] ?? null,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'car' => [
                    'id' => $car->id,
                    'name' => $car->name,
                    'matricule' => $car->matricule ?? null,
                    'model' => $car->model ?? null,
                    'year' => $car->year ?? null,
                ],
                'stats' => $stats,
                'history' => $history,
            ]);
        }

        return view('admin.data.kilometrage-vehicle-details', [
            'car' => $car,
            'stats' => $stats,
            'history' => $history,
        ]);
    }

    public function storeKilometrageEntry(Request $request)
    {
        $data = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'recorded_at' => ['required', 'date'],
            'kilometers' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $entry = KilometrageEntry::create([
            'car_id' => (int) $data['car_id'],
            'created_by' => auth()->id(),
            'recorded_at' => $data['recorded_at'],
            'kilometers' => $data['kilometers'],
            'note' => $data['note'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $entry->id]);
        }

        return redirect()->back()->with('success', 'Kilométrage ajouté.');
    }

    public function requests()
    {
        // Load ALL demandes so the page always shows data.
        // The pending filter is available via the status dropdown in the view.
        $requests = Demande::with(['user', 'car'])
            ->latest()
            ->get();

        $pendingCount = $requests->where('status', 'pending')->count();
        $conflictCount = $requests->where('has_conflict', true)->where('status', 'pending')->count();

        $title = 'Demandes';
        if ($pendingCount > 0) {
            $title .= " ({$pendingCount} en attente)";
        }
        if ($conflictCount > 0) {
            $title .= " — {$conflictCount} conflit(s)";
        }

        return view('admin.data.section', [
            'title'        => $title,
            'icon'         => 'fas fa-file-alt',
            'tableView'    => 'admin.tables.requests',
            'requests'     => $requests,
        ]);
    }

    public function reservations(Request $request)
    {
        $statusFilter = trim((string) $request->query('status', 'all'));
        $statuses = collect(explode(',', $statusFilter))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();

        $query = Demande::with(['user', 'car']);
        if ($statusFilter !== '' && $statusFilter !== 'all' && count($statuses) > 0) {
            $query->whereIn('status', $statuses);
        }

        $reservations = $query->get();

        return view('admin.data.section', [
            'title' => 'Réservations',
            'icon' => 'fas fa-calendar-check',
            'tableView' => 'admin.tables.reservations',
            'reservations' => $reservations,
        ]);
    }

    public function planningWindows()
    {
        $windows = PlanningWindow::all();

        return view('admin.data.section', [
            'title' => 'Fenêtres de planification',
            'icon' => 'fas fa-window-maximize',
            'tableView' => 'admin.tables.planning-windows',
            'windows' => $windows,
        ]);
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('admin.data.section', [
            'title' => 'Notifications',
            'icon' => 'fas fa-bell',
            'tableView' => 'admin.tables.notifications',
            'notifications' => $notifications,
        ]);
    }

    /**
     * Update a user from the admin data management Users tab.
     * Accepts AJAX (JSON) PUT requests.
     */
    public function updateUser(Request $request, User $user)
    {
        $actor = $request->user();

        // Protect super_admin accounts from being downgraded by non-super_admins
        if ($user->hasRole('super_admin') && !$actor->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Seul un Super Admin peut modifier ce compte.',
            ], 403);
        }

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'service' => ['nullable', 'string', 'max:255'],
            'role'    => ['required', Rule::in(['employee', 'admin', 'super_admin'])],
        ]);

        // Only super_admin can assign super_admin role
        if ($validated['role'] === 'super_admin' && !$actor->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Seul un Super Admin peut attribuer le rôle Super Admin.',
            ], 403);
        }

        $user->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'service' => $validated['service'] ?? null,
        ]);
        $user->syncRoles([$validated['role']]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Utilisateur \u00ab {$user->name} \u00bb mis à jour avec succès.",
            ]);
        }

        return redirect()->route('admin.data.index')->with('success', "Utilisateur \u00ab {$user->name} \u00bb mis à jour.");
    }

    /**
     * Disable a user account (soft deactivation - not deletion).
     * Accepts AJAX (JSON) DELETE requests.
     */
/**
 * Supprimer définitivement un utilisateur.
 * Vérifie d'abord qu'il n'a pas de réservations liées.
 */
    public function destroyUser(Request $request, User $user)
    {
        $actor = $request->user();

        // Empêcher la suppression de soi-même
        if ($user->id === $actor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 422);
        }

        // Empêcher la suppression d’un Super Admin
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Le compte Super Admin ne peut pas être supprimé.',
            ], 403);
        }

        // Vérifier s’il existe des réservations liées
        if ($user->reservations()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer le compte « {$user->name} » : des réservations existent. Désactivez le compte à la place.",
            ], 422);
        }

        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Compte « {$userName} » supprimé définitivement.",
        ]);
    }


    /**
     * Activate (reactivate) a disabled user account.
     * Accepts AJAX (JSON) POST requests.
     */
    public function activateUser(Request $request, User $user)
    {
        $actor = $request->user();

        // Only super_admin can reactivate accounts
        if (!$actor->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Seul un Super Admin peut réactiver un compte.',
            ], 403);
        }

        if ($user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte est déjà actif.',
            ], 422);
        }

        $userName = $user->name;
        $user->enable();

        return response()->json([
            'success' => true,
            'message' => "Compte de \u00ab {$userName} \u00bb réactivé avec succès.",
        ]);
    }
}
