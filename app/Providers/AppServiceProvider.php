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

            // Content scopes
            'content:read' => 'Read content',
            'content:write' => 'Create or update content',
            'content:delete' => 'Delete content',

            // Content Type scopes
            'content-type:read' => 'Read content types',
            'content-type:write' => 'Create or update content types',
            'content-type:delete' => 'Delete content types',

            // Field scopes
            'field:read' => 'Read fields',
            'field:write' => 'Create or update fields',
            'field:delete' => 'Delete fields',

            // Taxonomy scopes
            'taxonomy:read' => 'Read taxonomies',
            'taxonomy:write' => 'Create or update taxonomies',
            'taxonomy:delete' => 'Delete taxonomies',

            // Term scopes
            'term:read' => 'Read terms',
            'term:write' => 'Create or update terms',
            'term:delete' => 'Delete terms',

            // Asset scopes
            'asset:read' => 'Read assets',
            'asset:write' => 'Upload or update assets',
            'asset:delete' => 'Delete assets',

            // Comment scopes
            'comment:read' => 'Read comments',
            'comment:write' => 'Create or update comments',
            'comment:delete' => 'Delete comments',
        ]);

        Passport::authorizationView(
            fn ($parameters) => Inertia::render('auth/oauth/authorize', [
                'request' => $parameters['request'],
                'authToken' => $parameters['authToken'],
                'client' => $parameters['client'],
                'user' => $parameters['user'],
                'scopes' => $parameters['scopes'],
            ])
        );
    }
}
