<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Demande;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Top destinations — toutes les demandes (données réelles, tri décroissant)
        $destinationColors = ['#2E7D32', '#43A047', '#66BB6A', '#FFA726', '#FF9800'];
        $destinationRaw = Demande::query()
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

        $maxDestination = $destinationRaw->isEmpty()
            ? 1
            : max(1, (int) $destinationRaw->max('total'));

        // Résumé mensuel — demandes créées durant le mois en cours
        $monthlyCreated = Demande::query()
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);

        $monthlyCounts = (clone $monthlyCreated)
            ->selectRaw(
                'COUNT(*) as total, ' .
                "SUM(CASE WHEN status = '" . Demande::STATUS_PENDING . "' THEN 1 ELSE 0 END) as pending, " .
                "SUM(CASE WHEN status = '" . Demande::STATUS_APPROVED . "' THEN 1 ELSE 0 END) as approved, " .
                "SUM(CASE WHEN status = '" . Demande::STATUS_REJECTED . "' THEN 1 ELSE 0 END) as rejected, " .
                "SUM(CASE WHEN status = '" . Demande::STATUS_CANCELLED . "' THEN 1 ELSE 0 END) as cancelled"
            )
            ->first();

        $monthlyTotal = (int) ($monthlyCounts->total ?? 0);
        $monthlyApproved = (int) ($monthlyCounts->approved ?? 0);
        $approvalRate = $monthlyTotal > 0
            ? (int) round(($monthlyApproved / $monthlyTotal) * 100)
            : 0;

        $vehiclesUsed = (clone $monthlyCreated)
            ->whereNotNull('car_id')
            ->distinct()
            ->count('car_id');

        $monthlySummary = [
            'total' => $monthlyTotal,
            'approved' => $monthlyApproved,
            'pending' => (int) ($monthlyCounts->pending ?? 0),
            'rejected' => (int) ($monthlyCounts->rejected ?? 0),
            'cancelled' => (int) ($monthlyCounts->cancelled ?? 0),
            'approval_rate' => $approvalRate,
            'vehicles_used' => $vehiclesUsed,
        ];

        $analytics = [
            'destinations' => $destinations,
            'max_destination' => $maxDestination,
            'monthly_summary' => $monthlySummary,
            'month_label' => Carbon::now()->locale('fr')->translatedFormat('F Y'),
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

