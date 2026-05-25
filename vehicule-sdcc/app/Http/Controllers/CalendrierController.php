<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Car;
use App\Services\OptionsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendrierController extends Controller
{
    public function __construct(
        private readonly OptionsService $optionsService
    ) {
    }

    public function index(Request $request)
    {
        // Get month and year from query parameters, default to current
        $month = $request->query('month');
        $year = $request->query('year');
        $selectedCar = $request->query('car', 'all');
        
        if ($month && $year) {
            $currentMonth = Carbon::createFromDate($year, $month, 1);
        } else {
            $currentMonth = Carbon::now();
        }
        
        $firstDay = $currentMonth->copy()->startOfMonth();
        $lastDay = $currentMonth->copy()->endOfMonth();
        
        // Load vehicles from database for accurate IDs.
        $vehicles = Car::query()
            ->select(['id', 'name', 'matricule'])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (Car $car) {
                return [(string) $car->id => [
                    'name' => $car->name,
                    'plate' => $car->matricule,
                    'car_id' => $car->id,
                ]];
            })
            ->toArray();

        $carsById = Car::query()
            ->select(['id', 'name', 'matricule'])
            ->get()
            ->mapWithKeys(function (Car $car) {
                return [$car->id => trim($car->name . ' (' . $car->matricule . ')')];
            })
            ->toArray();

        $statusByDate = $this->buildStatusByDate($firstDay, $lastDay, $selectedCar, $carsById);
        
        return view('calendrier.index', [
            'currentMonth' => $currentMonth,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'statusByDate' => $statusByDate,
            'today' => Carbon::now(),
            'vehicles' => $vehicles,
            'selectedCar' => $selectedCar,
            'calendarOptions' => $this->optionsService->calendarOptions(),
        ]);
    }

    private function buildStatusByDate(Carbon $startDate, Carbon $endDate, string $selectedCar, array $carsById): array
    {
        $query = Demande::query()
            ->with(['user:id,name', 'car:id,name,matricule'])
            ->whereDate('start_date', '<=', $endDate->toDateString())
            ->whereDate('end_date', '>=', $startDate->toDateString());

        if ($selectedCar !== 'all') {
            $query->where('car_id', (int) $selectedCar);
        }

        $demandes = $query->get();

        $grouped = collect();
        foreach ($demandes as $demande) {
            foreach ($this->datesCoveredByDemande($demande, $startDate, $endDate) as $dateKey) {
                if (!$grouped->has($dateKey)) {
                    $grouped[$dateKey] = collect();
                }
                $grouped[$dateKey]->push($demande);
            }
        }

        $statusByDate = [];
        $cursor = $startDate->copy();
        while ($cursor <= $endDate) {
            $date = $cursor->format('Y-m-d');
            $dayDemandes = $grouped->get($date, collect());
            $statusByDate[$date] = $this->resolveStatusForDay($dayDemandes, $carsById, $date);
            $cursor->addDay();
        }

        return $statusByDate;
    }

    /**
     * @return list<string> Y-m-d keys for each day in [start_date, end_date] within the visible month
     */
    private function datesCoveredByDemande(Demande $demande, Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $start = Carbon::parse($demande->start_date)->startOfDay();
        $end = Carbon::parse($demande->end_date)->startOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $cursor = $start->copy()->max($rangeStart->copy()->startOfDay());
        $last = $end->copy()->min($rangeEnd->copy()->startOfDay());

        if ($cursor->gt($last)) {
            return [];
        }

        $dates = [];
        while ($cursor <= $last) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        return $dates;
    }

    private function resolveStatusForDay($dayDemandes, array $carsById, string $currentDate): array
    {
        $calendarOptions = $this->optionsService->calendarOptions();
        $statusLabels = $calendarOptions['status_labels_fr'] ?? $calendarOptions['status_labels_en'];
        $dayStatusLabels = $calendarOptions['day_status_labels'];
        $reservedMessage = $this->optionsService->reservationMessages()['day_reserved'];

        if ($dayDemandes->isEmpty()) {
            return [
                'key' => 'available',
                'label' => $dayStatusLabels['available'],
                'blocked' => false,
                'message' => null,
                'reservations' => [],
            ];
        }

        $reservations = $dayDemandes->map(function ($demande) use ($carsById, $statusLabels, $dayStatusLabels, $currentDate) {
            $normalizedStatus = strtolower((string) $demande->status);
            $start = Carbon::parse($demande->start_date)->startOfDay();
            $end = Carbon::parse($demande->end_date)->startOfDay();
            $day = Carbon::parse($currentDate)->startOfDay();

            $rangePosition = 'single';
            if ($end->gt($start)) {
                if ($day->equalTo($start)) {
                    $rangePosition = 'start';
                } elseif ($day->equalTo($end)) {
                    $rangePosition = 'end';
                } else {
                    $rangePosition = 'middle';
                }
            }

            $statusLabel = match ($normalizedStatus) {
                'approved' => $dayStatusLabels['approved'],
                'pending' => $dayStatusLabels['pending'],
                'cancelled', 'rejected' => $dayStatusLabels['cancelled'],
                default => ucfirst($normalizedStatus ?: $dayStatusLabels['pending']),
            };

            $vehicleName = $demande->car
                ? trim($demande->car->name . ' (' . $demande->car->matricule . ')')
                : ($demande->car_id ? ($carsById[$demande->car_id] ?? ('Véhicule #' . $demande->car_id)) : 'N/A');

            $period = $start->format('d/m/Y') . ' → ' . $end->format('d/m/Y');

            return [
                'status' => $normalizedStatus,
                'status_label' => $statusLabel,
                'display_label' => $statusLabel,
                'range_position' => $rangePosition,
                'employee' => $demande->user?->name ?? 'N/A',
                'vehicle' => $vehicleName,
                'date' => $currentDate,
                'period' => $period,
                'start_date' => $start->format('d/m/Y'),
                'end_date' => $end->format('d/m/Y'),
                'destination' => $demande->destination ?: 'N/A',
                'start_time' => (string) ($demande->start_time ?? ''),
                'end_time' => (string) ($demande->end_time ?? ''),
                'return_time' => (string) ($demande->return_time ?? ''),
                'reason' => $demande->reason ?: 'N/A',
            ];
        })->values()->toArray();

        $statuses = $dayDemandes->pluck('status')->map(fn ($s) => strtolower((string) $s));

        if ($statuses->contains('approved')) {
            return [
                'key' => 'approved',
                'label' => $dayStatusLabels['approved'],
                'blocked' => true,
                'message' => $reservedMessage,
                'reservations' => $reservations,
            ];
        }

        if ($statuses->contains('pending')) {
            return [
                'key' => 'pending',
                'label' => $dayStatusLabels['pending'],
                'blocked' => true,
                'message' => $reservedMessage,
                'reservations' => $reservations,
            ];
        }

        if ($statuses->contains('cancelled') || $statuses->contains('rejected')) {
            return [
                'key' => 'cancelled',
                'label' => $dayStatusLabels['cancelled'],
                'blocked' => true,
                'message' => $reservedMessage,
                'reservations' => $reservations,
            ];
        }

        return [
            'key' => 'pending',
            'label' => $dayStatusLabels['pending'],
            'blocked' => true,
            'message' => $reservedMessage,
            'reservations' => $reservations,
        ];
    }
}
