<?php

namespace App\Notifications\Traits;

use Illuminate\Support\Facades\Log;

/**
 * RoleAwareNotification Trait
 * 
 * Provides role-based filtering for notifications.
 * Allows notifications to specify which user roles should receive them.
 * 
 * Usage:
 *   class MyNotification extends Notification {
 *       use RoleAwareNotification;
 *       
 *       protected array $allowedRoles = ['admin', 'super_admin'];
 *   }
 */
trait RoleAwareNotification
{
    /**
     * Internal storage for allowed roles to avoid property name collisions
     * with notification classes that may declare their own `$allowedRoles`.
     *
     * @var array
     */
    private array $__roleAware_allowedRoles = [];

    /**
     * Check if a user should receive this notification based on role.
     * 
     * @param object $notifiable - The user object
     * @return bool
     */
    public function shouldNotify(object $notifiable): bool
    {
        // Determine allowed roles: trait internal setting takes precedence,
        // otherwise fall back to a class-declared `$allowedRoles` if present.
        $allowed = !empty($this->__roleAware_allowedRoles)
            ? $this->__roleAware_allowedRoles
            : (property_exists($this, 'allowedRoles') ? $this->allowedRoles : []);

        // If no role restrictions, notify everyone
        if (empty($allowed)) {
            return true;
        }

        // Check if user has any of the allowed roles
        foreach ($allowed as $role) {
            if ($notifiable->hasRole($role)) {
                return true;
            }
        }

        Log::debug('Notification blocked by role filter', [
            'user_id' => $notifiable->id,
            'user_roles' => $notifiable->getRoleNames()->toArray(),
            'allowed_roles' => $this->allowedRoles,
        ]);

        return false;
    }

    /**
     * Set allowed roles for this notification.
     * 
     * @param array|string $roles
     * @return self
     */
    public function forRoles(array|string $roles): self
    {
        $this->__roleAware_allowedRoles = is_array($roles) ? $roles : [$roles];
        return $this;
    }

    /**
     * Get the notification's delivery channels.
     * Override in child class if needed.
     * 
     * @param object $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        if (!$this->shouldNotify($notifiable)) {
            return [];
        }

        return ['database', 'mail'];
    }
}
