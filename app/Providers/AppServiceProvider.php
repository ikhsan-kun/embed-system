<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Kreait\Firebase\Database::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDatabaseUri(config('firebase.database.url'));
            return $factory->createDatabase();
        });

        // optional: keep the old string alias if other parts of app use it
        $this->app->alias(\Kreait\Firebase\Database::class, 'firebase.database');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
