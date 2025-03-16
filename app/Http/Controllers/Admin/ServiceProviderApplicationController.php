<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Enums\UserRoles;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use App\Events\NotificationSent;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Actions\GenerateNotificationAction;

class ServiceProviderApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = ProviderProfile::whereNull('verified_at')->with(['serviceType:id,name', 'profile.user'])->latest()->paginate(10);

        // dd($providers);

        return Inertia::render('Users/Admin/ServiceProviderApplication/Index', compact(['providers']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $providerProfile = ProviderProfile::where('id', $id)
            ->with(['profile', 'serviceType:id,name', 'profile.user.services'])->first();

        return Inertia::render('Users/Admin/ServiceProviderApplication/Show', compact(['providerProfile']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function delete(string $id)
    {
        $providerProfile = ProviderProfile::find($id);

        return Inertia::render('Users/Admin/ServiceProviderApplication/Delete', compact(['providerProfile']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }

    public function approve(string $id)
    {
        $providerProfile = ProviderProfile::find($id);

        return Inertia::render('Users/Admin/ServiceProviderApplication/Approve', compact(['providerProfile']));
    }


    public function approved(string $id)
    {
        $provider = ProviderProfile::find($id);

        $serviceProviderRole = Role::where('name', UserRoles::SERVICEPROVIDER->value)->first();

        $user = $provider->profile->user;

        $provider->update([
            'verified_at' => now(),
            'status' => 'approved'
        ]);

        $user->assignRole($serviceProviderRole);

        $notification = Notification::create([
            'user_id' => $user->id,
            'content' => GenerateNotificationAction::handle('application', 'application-approved'),
            'type' => 'application',
            'url' => '/'
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return to_route('admin.applications.index')->with('message_success', 'You have approved the application.');
    }

    public function reject(string $id)
    {
        $provider = ProviderProfile::find($id);

        $provider->delete();

        $user = $provider->profile->user;

        $notification = Notification::create([
            'user_id' => $user->id,
            'content' => GenerateNotificationAction::handle('application', 'application-rejected'),
            'type' => 'application',
            'url' => '/'
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return back()->with('message_success', 'You have rejected the application.');
    }
}
