<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait OptimizedQueries
 *
 * Provides optimized query patterns with eager loading to prevent N+1 queries
 * and improve application performance.
 *
 * Usage:
 *   use OptimizedQueries;
 *   
 *   $demandes = Demande::withOptimizations()->get();
 *   $users = User::withOptimizations()->get();
 *   $cars = Car::withOptimizations()->get();
 */
trait OptimizedQueries
{
    /**
     * Scope for eager loading related models
     *
     * - Demande: user, car, notifications
     * - User: roles, permissions
     * - Car: reservations
     */
    public function scopeWithOptimizations(Builder $query): Builder
    {
        // Get class name to determine which optimizations to apply
        $modelClass = class_basename($this);

        return match ($modelClass) {
            'Demande' => $query->with(['user', 'car', 'notifications']),
            'User' => $query->with(['roles:id,name']),
            'Car' => $query->with(['demandes' => function ($q) {
                $q->where('status', 'approved')->latest();
            }]),
            'PlanningZone' => $query->with(['cars', 'planningWindows']),
            default => $query,
        };
    }

    /**
     * Scope for minimal eager loading (only essential relations)
     * Useful for API responses where payload matters
     */
    public function scopeWithMinimal(Builder $query): Builder
    {
        $modelClass = class_basename($this);

        return match ($modelClass) {
            'Demande' => $query->with(['user:id,name,email', 'car:id,name,matricule']),
            'User' => $query->with(['roles:id,name']),
            'Car' => $query->select(['id', 'name', 'matricule', 'status', 'availability_type']),
            default => $query,
        };
    }
}
