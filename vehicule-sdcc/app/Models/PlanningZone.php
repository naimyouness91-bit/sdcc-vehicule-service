<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class PlanningZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'team',
    ];

    /**
     * Relationship: Zone has many employees through pivot table
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'planning_zone_user')->withTimestamps();
    }

    /**
     * Relationship: Zone has many vehicles through pivot table
     */
    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'planning_zone_car')->withTimestamps();
    }

    /**
     * Scope: Get only active zones
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Get all vehicles assigned to this zone
     */
    public function getAssignedVehicles()
    {
        return $this->cars()->where('status', 'disponible')->get();
    }

    /**
     * Check if a vehicle is assigned to this zone
     */
    public function hasVehicle(Car $car): bool
    {
        return $this->cars()->where('cars.id', $car->id)->exists();
    }
}


