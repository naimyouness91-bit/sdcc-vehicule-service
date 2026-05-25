<?php

namespace App\Http\Controllers;

use App\Models\PlanningZone;
use App\Models\User;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|super_admin');
    }

    /**
     * Display a listing of all zones with management interface
     */
    public function index()
    {
        $zones = PlanningZone::withCount(['users', 'cars'])
            ->orderBy('name')
            ->get();
        $employees = User::role('employee')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        $cars = Car::select('id', 'name', 'model', 'matricule')
            ->orderBy('name')
            ->get();
        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('zones.index', compact('zones', 'employees', 'cars', 'users'));
    }

    /**
     * Show the form for creating a new zone
     */
    public function create()
    {
        return view('zones.create');
    }

    /**
     * Store a newly created zone in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:planning_zones',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,in_progress,inactive',
        ]);

        $zone = PlanningZone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', "Zone '{$zone->name}' créée avec succès.");
    }

    /**
     * Show the form for editing the specified zone
     */
    public function edit(PlanningZone $zone)
    {
        $employees = User::role('employee')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        $cars = Car::select('id', 'name', 'model', 'matricule')
            ->orderBy('name')
            ->get();
        
        // Load existing relationships with specific columns
        $zone->load('users:id,name,email', 'cars:id,name,model,matricule');

        return view('zones.edit', compact('zone', 'employees', 'cars'));
    }

    /**
     * Update the specified zone in storage
     */
    public function update(Request $request, PlanningZone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:planning_zones,name,' . $zone->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,in_progress,inactive',
            'team' => 'nullable|string|max:100',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
            'car_ids' => 'nullable|array',
            'car_ids.*' => 'integer|exists:cars,id',
        ]);

        // Update zone details (use null coalescing to avoid undefined index)
        $zone->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'team' => $validated['team'] ?? null,
        ]);

        // Sync users with zone
        $userIds = $validated['user_ids'] ?? [];
        $zone->users()->sync($userIds);
        
        // Update planning_zone_id for assigned users
        if (!empty($userIds)) {
            User::whereIn('id', $userIds)->update(['planning_zone_id' => $zone->id]);
        }
        
        // Auto-assign users to zones based on team changes
        $this->autoAssignUsersToZones($validated['team'] ?? null, $zone->id);
        
        // Remove planning_zone_id from unassigned users
        User::where('planning_zone_id', $zone->id)
            ->whereNotIn('id', $userIds)
            ->update(['planning_zone_id' => null]);

        // Sync cars with zone
        $carIds = $validated['car_ids'] ?? [];
        $zone->cars()->sync($carIds);

        return redirect()->route('zones.index')
            ->with('success', "Zone '{$zone->name}' mise à jour avec succès.");
    }

    /**
     * Remove the specified zone from storage
     */
    public function destroy(PlanningZone $zone)
    {
        $zoneName = $zone->name;
        
        // First, remove the zone assignment from all users
        User::where('planning_zone_id', $zone->id)->update(['planning_zone_id' => null]);
        
        // Detach all users and cars from the pivot tables
        $zone->users()->detach();
        $zone->cars()->detach();
        
        // Finally, delete the zone
        $zone->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Zone '{$zoneName}' supprimée avec succès.",
            ]);
        }

        return redirect()->route('zones.index')
            ->with('success', "Zone '{$zoneName}' supprimée avec succès.");
    }

    /**
     * Assign users to a zone
     */
    public function assignUsers(Request $request, PlanningZone $zone)
    {
        $validated = $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $userIds = $validated['user_ids'] ?? [];

        // Sync users with pivot table (many-to-many)
        $zone->users()->sync($userIds);

        // Update planning_zone_id for assigned users
        if (!empty($userIds)) {
            User::whereIn('id', $userIds)->update(['planning_zone_id' => $zone->id]);
        }

        // Remove planning_zone_id from users no longer assigned
        User::where('planning_zone_id', $zone->id)
            ->whereNotIn('id', $userIds)
            ->update(['planning_zone_id' => null]);

        return redirect()->back()
            ->with('success', "Utilisateurs assignés à la zone '{$zone->name}'.");
    }

    /**
     * Assign cars to a zone
     */
    public function assignCars(Request $request, PlanningZone $zone)
    {
        $validated = $request->validate([
            'car_ids' => 'nullable|array',
            'car_ids.*' => 'integer|exists:cars,id',
        ]);

        $carIds = $validated['car_ids'] ?? [];

        // Sync cars with pivot table (many-to-many)
        $zone->cars()->sync($carIds);

        return redirect()->back()
            ->with('success', "Véhicules assignés à la zone '{$zone->name}'.");
    }

    /**
     * Auto-assign users to zones based on team changes
     */
    private function autoAssignUsersToZones(?string $team, int $currentZoneId = null): void
    {
        if (!$team) {
            return;
        }

        // Find the zone that matches this team name
        // Case-insensitive match in a DB-agnostic way
        $matchingZone = PlanningZone::whereRaw('LOWER(name) = ?', [Str::lower($team)])->first();

        if ($matchingZone && $matchingZone->id !== $currentZoneId) {
            // Get all users who should be in this zone and are not already assigned
            $usersToAssign = User::whereRaw('LOWER(team) = ?', [Str::lower($team)])
                ->whereNull('planning_zone_id')
                ->pluck('id')
                ->toArray();

            if (!empty($usersToAssign)) {
                // Update direct FK for these users
                User::whereIn('id', $usersToAssign)->update(['planning_zone_id' => $matchingZone->id]);

                // Sync pivot in one call
                $matchingZone->users()->syncWithoutDetaching($usersToAssign);
            }
        }
    }

    /**
     * Get zones for API response (AJAX)
     */
    public function getZones()
    {
        $request = request();
        $query = PlanningZone::query()->orderBy('name');

        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 25);
        $page = (int) $request->query('page', 1);

        if ($perPage > 0) {
            $paginator = $query->withCount(['users as employees_count', 'cars as vehicles_count'])->paginate($perPage, ['*'], 'page', $page);
            $data = $paginator->getCollection()->map(function ($z) {
                return [
                    'id' => $z->id,
                    'name' => $z->name,
                    'description' => $z->description,
                    'employees_count' => $z->employees_count ?? 0,
                    'vehicles_count' => $z->vehicles_count ?? 0,
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

        $zones = $query->withCount(['users as employees_count', 'cars as vehicles_count'])->get()->map(function ($z) {
            return [
                'id' => $z->id,
                'name' => $z->name,
                'description' => $z->description,
                'employees_count' => $z->employees_count ?? 0,
                'vehicles_count' => $z->vehicles_count ?? 0,
            ];
        });

        return response()->json([
            'data' => $zones,
            'meta' => [
                'total' => $zones->count(),
                'per_page' => $zones->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]
        ]);
    }

    /**
     * Get users assigned to a zone (AJAX)
     */
    public function getZoneUsers(PlanningZone $zone)
    {
        $users = $zone->users()->get();
        return response()->json($users);
    }
}
