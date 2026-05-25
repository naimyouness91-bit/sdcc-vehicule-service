<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        // Avoid stale bootstrap caches breaking tests (CSRF, route names, etc.).
        $cachedFiles = glob(__DIR__.'/../bootstrap/cache/routes-*.php') ?: [];
        $configCache = __DIR__.'/../bootstrap/cache/config.php';
        if (is_file($configCache)) {
            $cachedFiles[] = $configCache;
        }
        foreach ($cachedFiles as $cached) {
            @unlink($cached);
        }

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
