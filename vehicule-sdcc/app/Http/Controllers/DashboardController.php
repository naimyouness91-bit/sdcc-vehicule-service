<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Demande;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return $this->adminDashboard();
        }

        return $this->employeeDashboard();
    }

    /**
     * Admin Dashboard View
     */
    private function adminDashboard()
    {
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();

        $monthlyDemandes = Demande::query()
            ->whereDate('start_date', '>=', $monthStart)
            ->whereDate('start_date', '<=', $monthEnd);

        // Overall counts (single optimized query)
        $overall = Demande::query()
            ->selectRaw(
                "COUNT(*) as total, " .
                "SUM(CASE WHEN status = '" . Demande::STATUS_PENDING . "' THEN 1 ELSE 0 END) as pending, " .
                "SUM(CASE WHEN status = '" . Demande::STATUS_APPROVED . "' THEN 1 ELSE 0 END) as approved, " .
                "SUM(CASE WHEN status = '" . Demande::STATUS_CANCELLED . "' THEN 1 ELSE 0 END) as cancelled"
            )
            ->first();

        $stats = [
            // legacy/monthly value kept for backward compatibility
            'total_demandes' => (clone $monthlyDemandes)->count(),
            // Use overall counts for the dashboard cards
            'total' => (int) ($overall->total ?? 0),
            'en_attente' => (int) ($overall->pending ?? 0),
            'approuvees' => (int) ($overall->approved ?? 0),
            'annulees' => (int) ($overall->cancelled ?? 0),
            // keep vehicules_dispo as before
            'vehicules_dispo' => Car::where('status', 'disponible')->count(),
        ];

        // Load the latest demandes for display. Keep query minimal and stable:
        // - order by creation time desc
        // - limit to the most recent 8 items (matches UI space)
        // - do not apply hidden filters so admin sees the same source of truth
        $demandes = Demande::query()
            ->with(['user:id,name,service', 'car:id,name,matricule'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(function (Demande $demande) {
                $status = strtolower((string) $demande->status);

                $statusLabel = match ($status) {
                    Demande::STATUS_APPROVED => 'Approuvé',
                    Demande::STATUS_PENDING => 'En attente',
                    default => 'Rejeté',
                };

                $vehicleName = $demande->car
                    ? trim($demande->car->name . ' ' . ($demande->car->matricule ?? ''))
                    : '—';

                return [
                    'id' => $demande->id,
                    'employee' => $demande->user?->name ?? 'N/A',
                    'service' => $demande->user?->service ?? 'N/A',
                    'destination' => $demande->destination ?? 'N/A',
                    'date_usage' => Carbon::parse($demande->start_date)->format('d/m/Y'),
                    'vehicle' => $vehicleName,
                    'status' => $statusLabel,
                ];
            })
            ->values()
            ->toArray();

        $destinationColors = ['#FFA726', '#4CAF50', '#FF9800', '#66BB6A', '#FFB74D'];
        $destinationRaw = (clone $monthlyDemandes)
            ->selectRaw('destination, COUNT(*) as total')
            ->whereNotNull('destination')
            ->where('destination', '!=', '')
            ->groupBy('destination')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $destinations = $destinationRaw->values()->map(function ($row, $index) use ($destinationColors) {
            return [
                'name' => $row->destination,
                'count' => (int) $row->total,
                'color' => $destinationColors[$index % count($destinationColors)],
            ];
        })->toArray();

        $maxDestination = max(1, (int) $destinationRaw->max('total'));

        $approvedCount = (clone $monthlyDemandes)->where('status', Demande::STATUS_APPROVED)->count();
        $approvalBase = max(1, $stats['total_demandes']);
        $approvalRate = (int) round(($approvedCount / $approvalBase) * 100);

        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $avgDurationDays = (clone $monthlyDemandes)
                ->selectRaw('AVG((julianday(end_date) - julianday(start_date)) + 1) as avg_days')
                ->value('avg_days');
        } else {
            $avgDurationDays = (clone $monthlyDemandes)
                ->selectRaw('AVG(DATEDIFF(end_date, start_date) + 1) as avg_days')
                ->value('avg_days');
        }

        $topVehicleRow = (clone $monthlyDemandes)
            ->selectRaw('car_id, COUNT(*) as total')
            ->whereNotNull('car_id')
            ->groupBy('car_id')
            ->orderByDesc('total')
            ->first();

        $topVehicle = 'N/A';
        if ($topVehicleRow) {
            $car = Car::query()->find($topVehicleRow->car_id);
            if ($car) {
                $topVehicle = trim($car->name . ' ' . ($car->matricule ?? ''));
            }
        }

        $topEmployeeRow = (clone $monthlyDemandes)
            ->selectRaw('user_id, COUNT(*) as total')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->first();

        $topEmployee = 'N/A';
        if ($topEmployeeRow) {
            $topEmployee = User::query()->whereKey($topEmployeeRow->user_id)->value('name') ?? 'N/A';
        }

        $totalKm = (int) ((clone $monthlyDemandes)->sum('kilometers') ?? 0);

        $analytics = [
            'destinations' => $destinations,
            'max_destination' => $maxDestination,
            'total_km' => number_format($totalKm, 0, ',', ' ') . ' km',
            'approval_rate' => $approvalRate . '%',
            'avg_duration' => number_format((float) ($avgDurationDays ?? 0), 1, '.', '') . ' jours',
            'top_vehicle' => $topVehicle,
            'top_employee' => $topEmployee,
        ];

        // Get data for modals
        $cars = Car::orderBy('name')->get();
        $users = User::where('id', '!=', auth()->id())
            ->with('roles')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact('stats', 'demandes', 'analytics', 'cars', 'users'));
    }

    /**
     * Employee Dashboard View
     */
    private function employeeDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'total_demandes' => Demande::where('user_id', $userId)->count(),
            'total' => Demande::where('user_id', $userId)->count(),
            'en_attente' => Demande::where('user_id', $userId)->where('status', Demande::STATUS_PENDING)->count(),
            'approuvees' => Demande::where('user_id', $userId)->where('status', Demande::STATUS_APPROVED)->count(),
            'annulees' => Demande::where('user_id', $userId)->where('status', Demande::STATUS_CANCELLED)->count(),
            'rejetees' => Demande::where('user_id', $userId)->whereIn('status', [Demande::STATUS_REJECTED])->count(),
            'vehicules_dispo' => Car::where('status', 'disponible')->count(),
        ];

        // Load demandes for this employee using the same source as Mes Demandes
        // - source: Demande model
        // - ordering: latest by created_at
        // - no hidden filters, no special pagination here (view may limit display)
        $demandes = Demande::query()
            ->where('user_id', $userId)
            ->with(['user:id,name,service', 'car:id,name,matricule,status'])
            ->latest('created_at')
            ->get()
            ->map(function (Demande $demande) {
                $status = strtolower((string) $demande->status);

                $statusLabel = match ($status) {
                    Demande::STATUS_APPROVED => 'Approuvé',
                    Demande::STATUS_PENDING => 'En attente',
                    default => 'Rejeté',
                };

                $vehicle = $demande->car ? [
                    'id' => $demande->car->id,
                    'name' => $demande->car->name,
                    'matricule' => $demande->car->matricule ?? null,
                    'status' => $demande->car->status ?? null,
                ] : null;

                $vehicleName = $demande->car
                    ? trim($demande->car->name . ' ' . ($demande->car->matricule ?? ''))
                    : '—';

                return [
                    'id' => $demande->id,
                    'destination' => $demande->destination,
                    'status' => $demande->status,
                    'status_label' => $statusLabel,
                    'created_at' => $demande->created_at?->toDateTimeString(),
                    'start_date' => $demande->start_date?->toDateString(),
                    'end_date' => $demande->end_date?->toDateString(),
                    'date_usage' => $demande->start_date ? Carbon::parse($demande->start_date)->format('d/m/Y') : null,
                    'start_time' => $demande->start_time ?? null,
                    'end_time' => $demande->end_time ?? null,
                    'reason' => $demande->reason ?? null,
                    'car' => $vehicle,
                    'vehicle' => $vehicleName,
                    'employee' => $demande->user?->name ?? null,
                    'service' => $demande->user?->service ?? null,
                ];
            })
            ->values()
            ->toArray();

        return view('dashboard', compact('stats', 'demandes'));
    }
}

