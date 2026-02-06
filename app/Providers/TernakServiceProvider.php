<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Ternak\TernakQueryService;
use App\Services\Ternak\TernakExportService;

class TernakServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TernakQueryService::class, function ($app) {
            return new TernakQueryService();
        });

        $this->app->singleton(TernakExportService::class, function ($app) {
            return new TernakExportService(
                $app->make(TernakQueryService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
