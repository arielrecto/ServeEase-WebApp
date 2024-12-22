<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $hasProfileSetup = $request->user()->profile ?? false;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'hasProfileSetup' => $hasProfileSetup,
                'roleName' => $request?->user()?->getRoleNames()?->toArray(),
                'isServiceProvider' => $request?->user()?->hasProviderProfile(),
                'isVerifiedProvider' => $request?->user()?->hasVerifiedProviderProfile(),
            ],
            'flash' => [
                'message_success' => $request->session()->get('message_success'),
                'message_error' => $request->session()->get('message_error'),
                'message_warning' => $request->session()->get('message_warning')
            ]
        ];
    }
}
