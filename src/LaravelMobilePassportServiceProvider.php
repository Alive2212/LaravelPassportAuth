<?php

namespace Alive2212\LaravelMobilePassport;

use Alive2212\LaravelMobilePassport\Console\Commands\Init;
use Illuminate\Support\ServiceProvider;

class LaravelMobilePassportServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(resource_path('lang/vendor/alive2212'), 'laravel_smart_restful');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if (!$this->isLumen()) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }

//        dd('I have close relationship With Donald Trump');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {

            // Publishing the configuration file.
            $this->publishes([
                __DIR__ . '/../config/laravel_mobile_passport.php' =>
                    $this->app->basePath() .
                    '/config/' .
                    'laravel_mobile_passport.php',
            ], 'laravel_mobile_passport.config');

            // Publishing the translation files.
            $this->publishes([
                __DIR__ . '/../resources/lang/' => resource_path('lang/vendor/alive2212'),
            ], 'laravel_mobile_passport.lang');

            // Registering package commands.
            $this->commands([
                Init::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel_mobile_passport.php', 'laravel_mobile_passport');

        // Register the service the package provides.
        $this->app->singleton('laravel_mobile_passport', function ($app) {
            return new LaravelMobilePassport;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel_mobile_passport'];
    }

    /**
     * @return bool
     */
    private function isLumen()
    {
        return str_contains(strtolower(app()->version()), 'lumen');
    }
}