<?php

namespace Abolaradev\GeoGuard;

use Abolaradev\GeoGuard\Commands\FlushIpBlackListCommand;
use Illuminate\Support\ServiceProvider;
use Abolaradev\GeoGuard\Commands\GeoGuardCommand;
use Abolaradev\GeoGuard\Commands\RefreshIpCommand;
use Abolaradev\GeoGuard\Commands\SyncIpBlackListCommand;
use Abolaradev\GeoGuard\Middlewares\GeoGuardMiddleware;

class GeoGuardServiceProvider extends ServiceProvider
{
     /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configuration.
        $this->mergeConfigFrom(
            __DIR__.'/../config/geo-guard.php',
            'geo-guard'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];

        // Register the GeoGuard middleware alias.
        $router->aliasMiddleware('geo-guard', GeoGuardMiddleware::class);

        // Publishing is only available when running from the console
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Publish the package configuration file
        $this->publishes([
            __DIR__.'/../config/geo-guard.php' => config_path('geo-guard.php'),
        ], ['geo-guard', 'geo-guard-config']);

        // Publish the package migrations
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['geo-guard', 'geo-guard-migrations']);

        // Register the package Artisan commands
        $this->commands([
            GeoGuardCommand::class,
            RefreshIpCommand::class,
            SyncIpBlackListCommand::class,
            FlushIpBlackListCommand::class
        ]);
    }
}
