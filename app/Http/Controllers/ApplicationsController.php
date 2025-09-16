<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Passport\ClientRepository;

class ApplicationsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('settings/applications/index', [
            'applications' => $request->user()->oauthApps()->get(
                [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ),
        ]);
    }

    public function create()
    {
        return Inertia::render('settings/applications/create');
    }

    public function store(Request $request)
    {
        // Validate and create the application
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uris' => 'required|string',
            'type' => 'nullable|in:authorization_code,personal_access,client_credentials,password',
            'is_confidential' => 'nullable|boolean',
            'device_flow' => 'nullable|boolean',
        ]);

        $redirectUris = collect(explode(',', (string) $request->input('redirect_uris')))
            ->map(fn($uri) => trim($uri))
            ->filter()
            ->values()
            ->all();

        /** @var \Laravel\Passport\ClientRepository */
        app(ClientRepository::class)
            ->createAuthorizationCodeGrantClient(
                user: $request->user(),
                name: $request->input('name'),
                redirectUris: $redirectUris,
                confidential: $request->input('confidential', true),
                enableDeviceFlow: $request->input('device_flow', false),
            );


        return redirect()->route('applications.index')->with('success', 'Application created successfully.');
    }

    public function edit($clientId)
    {
        // Logic to show the edit form for an application would go here
        return Inertia::render('settings/applications/edit', [
            'application' => auth()->user()->oauthApps()->where('id', $clientId)->first(),
        ]);
    }

    public function destroy(Request $request, $clientId): RedirectResponse
    {
        $user = $request->user();

        $client = $user->oauthApps()->where('id', $clientId)->first();
        $client->delete();

        return back();
    }
}
