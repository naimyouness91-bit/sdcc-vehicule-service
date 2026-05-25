<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Demande::class => \App\Policies\DemandPolicy::class,
        \App\Models\Notification::class => \App\Policies\NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Notification-related gates for user isolation
        Gate::define('access-notifications', function ($user) {
            // All authenticated users can access their own notifications
            return true;
        });

        Gate::define('view-admin-notifications', function ($user) {
            // Only admins and super_admins can view the admin notifications page
            return $user->hasAnyRole(['admin', 'super_admin']);
        });

        Gate::define('manage-notifications', function ($user) {
            // All authenticated users can manage (read/delete) their own notifications
            return true;
        });
    }
}
