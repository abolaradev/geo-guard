<?php

namespace Abolaradev\GeoGuard;

use Illuminate\Support\ServiceProvider;
use Abolaradev\GeoGuard\Commands\GeoGuardCommand;
use Livewire\Livewire;
use Illuminate\Support\Facades\Blade;

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

        // Register the package views as a Livewire namespace
        Livewire::addNamespace(
            namespace: 'geo-guard',
            viewPath: __DIR__ . '/../resources/views/livewire',
        );

        // Register the package Blade components namespace
        Blade::componentNamespace(__DIR__ . '/../resources/views/components','geo-guard');

        // Load the package routes
        $this->loadRoutesFrom(__DIR__.'/../routes/geo-guard.php');

        // Load the package views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'geo-guard');

        // Publishing is only available when running from the console
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Publish the package configuration file
        $this->publishes([
            __DIR__.'/../config/geo-guard.php' => config_path('geo-guard.php'),
        ], ['geo-guard', 'geo-guard-config']);

        // Publish the package views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/geo-guard'),
        ], ['geo-guard', 'geo-guard-views']);

        // Publish the package assets
        $this->publishes([
            __DIR__.'/../resources/dist' => public_path('vendor/geo-guard'),
        ], ['geo-guard', 'geo-guard-assets']);

        // Publish the package migrations
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['geo-guard', 'geo-guard-migrations']);

        // Register the package Artisan commands
        $this->commands([
            GeoGuardCommand::class
        ]);
    }
}
