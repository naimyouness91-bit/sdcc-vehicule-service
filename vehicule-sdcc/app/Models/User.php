<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Event;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // Use the parent constructor and Eloquent trait booting behavior.
    // The custom constructor previously interfered with Laravel's
    // trait initializer setup and caused "Undefined array key"
    // errors during authentication. Rely on the framework's
    // initialization instead of overriding the constructor here.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'service',
        'team',
        'planning_zone_id',
        // 'is_active' is intentionally not mass assignable to prevent privilege escalation
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEmployee(): bool
    {
        return !$this->isSuperAdmin() && !$this->isAdmin();
    }

    public function primaryRole(): string
    {
        return (string) optional($this->roles->first())->name ?: 'employee';
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class);
    }

    /**
     * Relationship: User belongs to a Planning Zone
     */
    public function planningZone(): BelongsTo
    {
        return $this->belongsTo(PlanningZone::class);
    }

    /**
     * Scope: Get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only inactive (deactivated) users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Get all users regardless of active status
     * Useful for admin queries that need to see deactivated users
     */
    public function scopeIncludingInactive($query)
    {
        return $query->withoutGlobalScopes(); // No filter applied
    }

    /**
     * Get the user's assigned planning zone
     */
    public function getZone(): ?PlanningZone
    {
        return $this->planningZone;
    }

    /**
     * Get vehicles available to this user through their zone
     */
    public function getAvailableVehicles()
    {
        $zone = $this->getZone();
        if (!$zone) {
            return collect([]);
        }
        return $zone->cars;
    }

    /**
     * Check if user can access a specific vehicle through their zone
     */
    public function canAccessVehicle(Car $car): bool
    {
        // Admins can access all vehicles
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        // Employees must have a zone
        $zone = $this->getZone();
        if (!$zone) {
            return false;
        }

        // Check if vehicle is assigned to user's zone
        return $zone->cars()->where('cars.id', $car->id)->exists();
    }

    /**
     * Automatically assign zone based on team
     */
    public function autoAssignZone(): bool
    {
        // Don't auto-assign if already has a zone
        if ($this->planning_zone_id) {
            return false;
        }

        // Don't auto-assign if no team is set
        if (!$this->team) {
            return false;
        }

        // Find zone matching the team name
        $zone = PlanningZone::where('name', 'ILIKE', $this->team)->first();
        
        if ($zone) {
            $this->planning_zone_id = $zone->id;
            $this->save();
            
            // Also sync user with zone's many-to-many relationship
            $zone->users()->syncWithoutDetaching([$this->id]);
            
            return true;
        }

        return false;
    }

    /**
     * Get zone based on team assignment
     */
    public static function getZoneByTeam(string $team): ?PlanningZone
    {
        return PlanningZone::where('name', 'ILIKE', $team)->first();
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Check if user account is inactive (disabled)
     */
    public function isInactive(): bool
    {
        return !$this->is_active;
    }

    /**
     * Disable user account (soft deactivation, not deletion)
     */
    public function disable(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Enable user account (reactivate)
     */
    public function enable(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Get account status label
     */
    public function getStatusLabel(): string
    {
        return $this->is_active ? 'Actif' : 'Désactivé';
    }

    /**
     * Get account status color for UI
     */
    public function getStatusColor(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    /**
     * Boot the model to handle automatic zone assignment
     */
    protected static function boot()
    {
        static::created(function ($user) {
            // Auto-assign zone based on team when user is created
            if ($user->team && !$user->planning_zone_id) {
                $user->autoAssignZone();
            }
        });

        static::updated(function ($user) {
            // Auto-assign zone based on team when user is updated
            if ($user->wasChanged('team') && !$user->planning_zone_id) {
                $user->autoAssignZone();
            }
        });
    }
}
