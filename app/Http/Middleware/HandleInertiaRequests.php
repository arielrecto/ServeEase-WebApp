<?php

namespace App\Http\Middleware;

use App\Models\User;
use Inertia\Middleware;
use App\Models\AvailService;
use App\Models\Notification;
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
        $finishedBookings = null;
        $user = null;
        $notifications = null;

        if (Auth::check()) {
            $finishedBookings = AvailService::with('service.user')
                ->whereStatus('completed')
                ->latest()
                ->get();

            $user = User::with(['profile'])
                ->whereId($request->user()->id)
                ->first();

            $notifications = Notification::latest()->take(8)->get();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'notifications' => $notifications,
                'hasProfileSetup' => $hasProfileSetup,
                'isAdmin' => $request?->user()?->getRoleNames()?->contains('admin'),
                'roleName' => $request?->user()?->getRoleNames()?->toArray(),
                'isServiceProvider' => $request?->user()?->hasProviderProfile(),
                'isVerifiedProvider' => $request?->user()?->hasVerifiedProviderProfile(),
                'finishedBookings' => $finishedBookings,
            ],
            'flash' => [
                'message_success' => $request->session()->get('message_success'),
                'message_error' => $request->session()->get('message_error'),
                'message_warning' => $request->session()->get('message_warning')
            ]
        ];
    }
}
