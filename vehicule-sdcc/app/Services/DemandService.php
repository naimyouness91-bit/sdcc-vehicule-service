<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Demande;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * DemandService
 * 
 * Centralized service for handling demand/reservation queries
 * with optimized eager loading and filtering.
 * 
 * This prevents N+1 queries and provides a single source of truth
 * for business logic related to demands.
 */
class DemandService
{
    /**
     * Get all demands with optimized eager loading
     */
    public function getAllDemands(array $filters = []): Collection
    {
        $query = Demande::with(['user:id,name,email,service', 'car:id,name,matricule,status']);

        // Filter by status if provided
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by user if provided
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by car if provided
        if (!empty($filters['car_id'])) {
            $query->where('car_id', $filters['car_id']);
        }

        // Filter by date range if provided
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('start_date', [
                $filters['start_date'],
                $filters['end_date'],
            ]);
        }

        return $query->latest()->get();
    }

    /**
     * Get demands for specific user with eager loading
     */
    public function getUserDemands($userId): Collection
    {
        return Demande::with(['car:id,name,matricule,status'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Get available cars for a specific date with optimized query
     */
    public function getAvailableCarsForDate(Carbon $date, ?int $userId = null): Collection
    {
        $allowedAvailability = $date->isWeekend()
            ? ['weekend', 'both']
            : ['both'];

        // Get reserved car IDs for this date
        $reservedCarIds = Demande::query()
            ->whereDate('start_date', $date->toDateString())
            ->whereNotNull('car_id')
            ->whereIn('status', ['approved', 'pending'])
            ->distinct('car_id')
            ->pluck('car_id')
            ->toArray();

        $query = Car::query()
            ->where('status', 'disponible')
            ->whereIn('availability_type', $allowedAvailability)
            ->when(!empty($reservedCarIds), fn ($q) => $q->whereNotIn('id', $reservedCarIds));

        // Filter by zone if user is employee
        $user = $userId ? User::find($userId) : auth()->user();
        if ($user && method_exists($user, 'isEmployee') && $user->isEmployee()) {
            $zone = method_exists($user, 'getZone') ? $user->getZone() : null;
            if ($zone) {
                $query->where('planning_zone_id', $zone->id);
            }
        }

        return $query->get();
    }

    /**
     * Find an existing reservation that overlaps the given car and date range.
     */
    public function findOverlappingReservation(
        int $carId,
        string $startDate,
        string $endDate,
        ?int $excludeDemandeId = null
    ): ?Demande {
        $query = Demande::query()
            ->where('car_id', $carId)
            ->whereIn('status', app(OptionsService::class)->reservationBlockingStatuses())
            ->whereRaw('? <= DATE(end_date)', [$startDate])
            ->whereRaw('DATE(start_date) <= ?', [$endDate]);

        if ($excludeDemandeId !== null) {
            $query->where('id', '!=', $excludeDemandeId);
        }

        return $query->orderBy('start_date')->first();
    }

    /**
     * Get conflicting demands for a car and date range
     */
    public function getConflictingDemands(int $carId, Carbon $startDate, Carbon $endDate): Collection
    {
        return Demande::with(['user:id,name,email'])
            ->where('car_id', $carId)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->get();
    }

    /**
     * Get statistics for dashboard with optimized queries
     */
    public function getDashboardStats(): array
    {
        return [
            'total_demands' => Demande::count(),
            'pending_demands' => Demande::where('status', 'pending')->count(),
            'approved_demands' => Demande::where('status', 'approved')->count(),
            'available_cars' => Car::where('status', 'disponible')->count(),
            'maintenance_cars' => Car::where('status', 'maintenance')->count(),
            'total_users' => User::where('is_active', true)->count(),
        ];
    }

    /**
     * Get demands needing attention (pending or soon-to-happen)
     */
    public function getAttentionNeededDemands(): Collection
    {
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7);

        return Demande::with(['user:id,name,email', 'car:id,name,matricule'])
            ->where(function ($query) use ($today, $sevenDaysFromNow) {
                // Pending demands (waiting for approval)
                $query->where('status', 'pending')
                    // OR approved demands starting soon
                    ->orWhere(function ($q) use ($today, $sevenDaysFromNow) {
                        $q->where('status', 'approved')
                            ->whereBetween('start_date', [$today, $sevenDaysFromNow]);
                    });
            })
            ->orderBy('start_date')
            ->get();
    }
}
