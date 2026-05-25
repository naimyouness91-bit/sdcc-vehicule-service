<?php

namespace App\Http\Controllers;

use App\Exports\PlanningExport;
use App\Exports\ReservationsHistoryExport;
use App\Exports\ReservationsTemplateExport;
use App\Models\Car;
use App\Models\Demande;
use App\Models\PlanningWindow;
use App\Models\PlanningZone;
use App\Models\User;
use App\Services\HrExcelSyncService;
use App\Services\OptionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PlanificationController extends Controller
{
    public function __construct(
        private readonly HrExcelSyncService $hrExcelSyncService,
        private readonly OptionsService $optionsService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $statusFilter = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));
        $zoneFilter = (int) $request->query('zone_id', 0);

        $zonesQuery = PlanningZone::query()
            ->withCount(['users', 'cars'])
            ->with(['users:id,name,service', 'cars:id,name,matricule,status,availability_type'])
            ->orderBy('name');

        if ($statusFilter !== 'all') {
            $zonesQuery->where('status', $statusFilter);
        }

        if ($search !== '') {
            $zonesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($zoneFilter > 0) {
            $zonesQuery->whereKey($zoneFilter);
        }

        $zones = $zonesQuery->get();

        $windows = PlanningWindow::query()
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->get();

        $zoneFilterOptions = PlanningZone::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $employees = User::role(['employee'])->orderBy('name')->get(['id', 'name', 'service']);
        $cars = Car::query()->orderBy('name')->get(['id', 'name', 'matricule', 'status', 'availability_type']);

        $resourcesAssigned = (int) DB::table('planning_zone_user')->count() + (int) DB::table('planning_zone_car')->count();
        $activeWindows = (int) PlanningWindow::query()->where('is_active', true)->count();
        $latestZoneUpdate = PlanningZone::query()->max('updated_at');
        $latestWindowUpdate = PlanningWindow::query()->max('updated_at');
        $latestUpdate = collect([$latestZoneUpdate, $latestWindowUpdate])->filter()->max();
        $planningVersion = $latestUpdate
            ? Carbon::parse($latestUpdate)->format('d/m/Y H:i')
            : 'v1.0';

        $stats = [
            'zones' => (int) PlanningZone::query()->count(),
            'resources' => $resourcesAssigned,
            'active_windows' => $activeWindows,
            'planning_version' => $planningVersion,
        ];

        // Map French status names to Demande constants
        $statusMap = [
            'en_attente' => Demande::STATUS_PENDING,
            'approuvee' => Demande::STATUS_APPROVED,
            'annulee' => Demande::STATUS_CANCELLED,
            'rejetee' => Demande::STATUS_REJECTED,
        ];

        // Load reservations (demandes) with relationships and apply status filter
        $demandesQuery = Demande::query()
            ->with(['user', 'car'])
            ->orderByDesc('created_at');

        // Filter by status if not 'all'
        if ($statusFilter !== 'all' && isset($statusMap[$statusFilter])) {
            $demandesQuery->where('status', $statusMap[$statusFilter]);
        }

        $demandes = $demandesQuery->get();

        // Transform demandes to format needed by JavaScript
        $reservations = $demandes->map(function ($demande) {
            return [
                'id' => $demande->id,
                'name' => $demande->user->name ?? 'Unknown',
                'email' => $demande->user->email ?? 'N/A',
                'destination' => $demande->destination ?? 'N/A',
                'date' => $demande->start_date ? $demande->start_date->format('Y-m-d') : 'N/A',
                'time' => $demande->start_time ?? 'N/A',
                'km' => $demande->kilometers ?? 0,
                'vehicle' => ($demande->car->matricule ?? 'N/A') . ' (' . ($demande->car->name ?? 'N/A') . ')',
                'status' => $demande->status ?? 'pending',
            ];
        })->toArray();

        return view('planification.index', [
            'stats' => $stats,
            'planningOptions' => $this->optionsService->planificationOptions(),
            'zones' => $zones,
            'windows' => $windows,
            'employees' => $employees,
            'cars' => $cars,
            'zoneFilterOptions' => $zoneFilterOptions,
            'zoneFilter' => $zoneFilter,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'reservations' => $reservations,
        ]);
    }

    public function export()
    {
        // Backward-compatible endpoint: always return the same live file.
        return $this->liveExcel();
    }

    public function liveExcel()
    {
        $this->hrExcelSyncService->ensureExcelFile();

        return Storage::disk($this->hrExcelSyncService->disk())->download(
            $this->hrExcelSyncService->excelRelativePath(),
            $this->hrExcelSyncService->downloadName()
        );
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,in_progress,inactive'],
        ]);

        $zone = PlanningZone::create($validated);

        return $request->expectsJson()
            ? response()->json([
                'zone' => $zone->fresh()
                    ->load(['users:id,name,service', 'cars:id,name,matricule,status,availability_type'])
                    ->loadCount(['users', 'cars']),
            ])
            : back()->with('success', 'Zone créée avec succès.');
    }

    public function updateZone(Request $request, PlanningZone $zone)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'in:active,in_progress,inactive'],
        ]);

        $zone->update($validated);

        return $request->expectsJson()
            ? response()->json(['zone' => $zone->fresh()->loadCount(['users', 'cars'])])
            : back()->with('success', 'Zone mise à jour.');
    }

    public function destroyZone(Request $request, PlanningZone $zone)
    {
        $zone->delete();

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Zone supprimée.');
    }

    public function syncAssignments(Request $request, PlanningZone $zone)
    {
        $validated = $request->validate([
            'user_ids' => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'car_ids' => ['array'],
            'car_ids.*' => ['integer', 'exists:cars,id'],
        ]);

        $zone->users()->sync($validated['user_ids'] ?? []);
        $zone->cars()->sync($validated['car_ids'] ?? []);

        return response()->json([
            'zone' => $zone->fresh()
                ->load(['users:id,name,service', 'cars:id,name,matricule,status,availability_type'])
                ->loadCount(['users', 'cars']),
        ]);
    }

    public function storeWindow(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $window = PlanningWindow::create([
            'name' => $validated['name'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        if ($window->is_active) {
            PlanningWindow::query()->whereKeyNot($window->id)->update(['is_active' => false]);
        }

        return response()->json(['window' => $window->fresh()]);
    }

    public function toggleWindow(Request $request, PlanningWindow $window)
    {
        $window->update(['is_active' => !$window->is_active]);
        if ($window->is_active) {
            PlanningWindow::query()->whereKeyNot($window->id)->update(['is_active' => false]);
        }

        return response()->json(['window' => $window->fresh()]);
    }

    public function destroyWindow(Request $request, PlanningWindow $window)
    {
        $window->delete();
        return response()->json(['ok' => true]);
    }

    public function exportPlanningExcel(Request $request)
    {
        $zones = PlanningZone::query()
            ->with(['users:id,name,service', 'cars:id,name,matricule'])
            ->orderBy('name')
            ->get();

        $windows = PlanningWindow::query()->orderByDesc('is_active')->orderByDesc('start_date')->get();

        return Excel::download(
            new PlanningExport($zones, $windows),
            'planning_' . Carbon::now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPlanningPdf(Request $request)
    {
        $zones = PlanningZone::query()
            ->with(['users:id,name,service', 'cars:id,name,matricule'])
            ->orderBy('name')
            ->get();

        $windows = PlanningWindow::query()->orderByDesc('is_active')->orderByDesc('start_date')->get();

        return view('planification.pdf', [
            'generatedAt' => Carbon::now(),
            'zones' => $zones,
            'windows' => $windows,
        ]);
    }

    /**
     * Display reservation history with filters
     */
    public function history(Request $request)
    {
        $filters = $this->historyFiltersFromRequest($request);
        $statusFilter = $filters['statusFilter'];
        $cardFilter = $this->resolveHistoryCardFilter($statusFilter);

        $baseQuery = $this->buildHistoryQuery($filters);
        $stats = $this->historyStatsFromQuery(clone $baseQuery);

        $reservationsQuery = clone $baseQuery;
        if ($statusFilter !== 'all') {
            $reservationsQuery->where('status', $statusFilter);
        }
        $reservations = $reservationsQuery->get();

        $employees = User::role(['employee'])
            ->orderBy('name')
            ->get(['id', 'name', 'service']);

        $cars = Car::query()
            ->orderBy('name')
            ->get(['id', 'name', 'matricule']);

        return view('planification.history', [
            'reservations' => $reservations,
            'employees' => $employees,
            'cars' => $cars,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'cardFilter' => $cardFilter,
            'employeeFilter' => $filters['employeeFilter'],
            'carFilter' => $filters['carFilter'],
            'dateFrom' => $filters['dateFrom'],
            'dateTo' => $filters['dateTo'],
            'search' => $filters['search'],
        ]);
    }

    /**
     * Filter reservation history by stat card (AJAX / JSON).
     */
    public function filterHistory(Request $request)
    {
        $filters = $this->historyFiltersFromRequest($request);
        $cardFilter = (string) $request->query('card', 'total');
        $statusFilter = $this->historyStatusFromCard($cardFilter);

        $baseQuery = $this->buildHistoryQuery($filters);
        $stats = $this->historyStatsFromQuery(clone $baseQuery);

        $reservationsQuery = clone $baseQuery;
        if ($statusFilter !== null) {
            $reservationsQuery->where('status', $statusFilter);
        }
        $reservations = $reservationsQuery->get();

        $html = view('planification.partials.history-table-body', [
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
     * @return array{
     *     statusFilter: string,
     *     employeeFilter: int,
     *     carFilter: int,
     *     dateFrom: ?string,
     *     dateTo: ?string,
     *     search: string
     * }
     */
    private function historyFiltersFromRequest(Request $request): array
    {
        $statusFilter = (string) $request->query('status', 'all');
        $card = (string) $request->query('card', '');

        if ($card !== '') {
            $mapped = $this->historyStatusFromCard($card);
            $statusFilter = $mapped ?? 'all';
        } elseif ($statusFilter !== 'all') {
            $statusFilter = $this->normalizeHistoryStatusFilter($statusFilter) ?? 'all';
        }

        return [
            'statusFilter' => $statusFilter,
            'employeeFilter' => (int) $request->query('employee_id', 0),
            'carFilter' => (int) $request->query('car_id', 0),
            'dateFrom' => $request->query('date_from'),
            'dateTo' => $request->query('date_to'),
            'search' => trim((string) $request->query('q', '')),
        ];
    }

    private function buildHistoryQuery(array $filters)
    {
        $query = Demande::query()
            ->with(['user:id,name,service', 'car:id,name,matricule,status'])
            ->orderByDesc('created_at');

        if ($filters['employeeFilter'] > 0) {
            $query->where('user_id', $filters['employeeFilter']);
        }

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
                $q->whereHas('user', function ($userQ) use ($search) {
                    $userQ->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('car', function ($carQ) use ($search) {
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
    private function historyStatsFromQuery($query): array
    {
        $collection = $query->get();

        return [
            'total' => $collection->count(),
            'pending' => $collection->where('status', Demande::STATUS_PENDING)->count(),
            'approved' => $collection->where('status', Demande::STATUS_APPROVED)->count(),
            'cancelled' => $collection->where('status', Demande::STATUS_CANCELLED)->count(),
        ];
    }

    private function historyStatusFromCard(string $card): ?string
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

    private function normalizeHistoryStatusFilter(string $status): ?string
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

    private function resolveHistoryCardFilter(string $statusFilter): string
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
     * Export reservation history as CSV
     */
    public function exportHistory(Request $request)
    {
        $statusFilter = (string) $request->query('status', 'all');
        $employeeFilter = (int) $request->query('employee_id', 0);
        $carFilter = (int) $request->query('car_id', 0);
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('q', ''));

        $query = \App\Models\Demande::query()
            ->with(['user:id,name,service', 'car:id,name,matricule,status'])
            ->orderByDesc('created_at');

        // Apply filters (same as history view)
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if ($employeeFilter > 0) {
            $query->where('user_id', $employeeFilter);
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
                $q->whereHas('user', function ($userQ) use ($search) {
                    $userQ->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('car', function ($carQ) use ($search) {
                    $carQ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%');
                })
                ->orWhere('destination', 'like', '%' . $search . '%')
                ->orWhere('reason', 'like', '%' . $search . '%');
            });
        }

        $reservations = $query->get();

        // Create CSV content
        $csvHeaders = [
            'N°',
            'Date de Demande',
            'Employé',
            'Service',
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
                $reservation->user?->name ?? 'N/A',
                $reservation->user?->service ?? 'N/A',
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
        
        $filename = 'historique_reservations_' . Carbon::now()->format('Ymd_His') . '.csv';

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
     * Export reservation history as Excel (.xlsx)
     */
    public function exportHistoryExcel(Request $request)
    {
        $statusFilter = (string) $request->query('status', 'all');
        $employeeFilter = (int) $request->query('employee_id', 0);
        $carFilter = (int) $request->query('car_id', 0);
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('q', ''));

        $query = \App\Models\Demande::query()
            ->with(['user:id,name,service', 'car:id,name,matricule,status'])
            ->orderByDesc('created_at');

        // Apply filters (same as history view)
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if ($employeeFilter > 0) {
            $query->where('user_id', $employeeFilter);
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
                $q->whereHas('user', function ($userQ) use ($search) {
                    $userQ->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('car', function ($carQ) use ($search) {
                    $carQ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%');
                })
                ->orWhere('destination', 'like', '%' . $search . '%')
                ->orWhere('reason', 'like', '%' . $search . '%');
            });
        }

        $reservations = $query->get();

        // Generate Excel file
        $filename = 'historique_reservations_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ReservationsHistoryExport($reservations),
            $filename
        );
    }

    /**
     * Export empty template for reservations import
     */
    public function exportHistoryTemplate(Request $request)
    {
        $filename = 'empty_template_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ReservationsTemplateExport(),
            $filename
        );
    }
}
