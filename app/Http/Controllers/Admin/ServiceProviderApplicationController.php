<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Remark;
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
        $providers = ProviderProfile::with(['serviceType', 'profile.user'])
            ->where('status', 'pending')
            ->whereNull('verified_at')
            ->latest()
            ->paginate(10)
            ->through(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->profile->user->name,
                    'service_type' => $provider->serviceType->name,
                    'service_type_id' => $provider->serviceType->id,
                    'experience' => $provider->experience,
                    'created_at' => $provider->created_at
                ];
            });

        // dd(
        //     $providers,
        //     ProviderProfile::with(['serviceType'])
        //         // ->where('status', 'pending')
        //         ->whereNull('verified_at')
        //         ->get()
        // );

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
            'url' => '/customer/dashboard'
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return to_route('admin.applications.index')->with('message_success', 'You have approved the application.');
    }

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'remark' => 'required|string'
        ]);


        $providerProfile = ProviderProfile::find($id);

        // Update status to rejected
        $providerProfile->update([
            'status' => 'rejected'
        ]);


        // Create remark
        Remark::create([
            'remarkable_id' => $providerProfile->id,
            'remarkable_type' => ProviderProfile::class,
            'user_id' => auth()->id(),
            'content' => $request->remark
        ]);

        // Create notification for the provider
        $notification = Notification::create([
            'user_id' => $providerProfile->profile->user->id,
            'content' => GenerateNotificationAction::handle('application', 'application-rejected', auth()->user()),
            'type' => 'application',
            'url' => '/customer/service-provider/create'
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return redirect()
            ->back()
            ->with('message_success', 'Application has been rejected');
    }
}
