<?php

namespace App\Providers;

use App\Services\OptionsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Demande;
use App\Observers\DemandeObserver;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OptionsService::class, fn () => new OptionsService());
        $this->app->singleton(\App\Services\DemandService::class, fn () => new \App\Services\DemandService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure Eloquent models are (re)booted correctly.
        // This clears any partially-booted state left by earlier failed boots
        // and prevents "Undefined array key 'App\\Models\\User'" errors.
        EloquentModel::clearBootedModels();

        // Force HTTPS in production (critical behind reverse proxies)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register model observers
        Demande::observe(DemandeObserver::class);
    }
}
