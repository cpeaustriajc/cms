<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'user:read' => 'Retrieve the user info',
        ]);

        Passport::authorizationView(
            fn($parameters) => Inertia::render('auth/oauth/authorize', [
                'request' => $parameters['request'],
                'authToken' => $parameters['authToken'],
                'client' => $parameters['client'],
                'user' => $parameters['user'],
                'scopes' => $parameters['scopes'],
            ])
        );
    }
}
