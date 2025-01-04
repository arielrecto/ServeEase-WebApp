<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Enums\UserRoles;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class ServiceProviderApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = ProviderProfile::whereVerifiedAt(null)->with(['profile.user'])->latest()->paginate(10);

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
            ->with(['profile', 'profile.user.services'])->first();

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


        // TODO: Add assign service provider role to user


        $user->assignRole($serviceProviderRole);

        return to_route('admin.applications.index')->with('message_success', 'You have approved the application.');
    }
}
