<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use NunoMaduro\Collision\Provider;
use App\Http\Controllers\Controller;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = ProviderProfile::whereVerifiedAt(null)->with(['profile.user'])->latest()->paginate(10);

        // dd($providers);

        return Inertia::render('Users/Admin/ServiceProvider/Index', compact(['providers']));
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

        return Inertia::render('Users/Admin/ServiceProvider/Show', compact(['providerProfile']));
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

        return Inertia::render('Users/Admin/ServiceProvider/Delete', compact(['providerProfile']));
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

        return Inertia::render('Users/Admin/ServiceProvider/Approve', compact(['providerProfile']));
    }


    public function approved(string $id)
    {
        $provider = ProviderProfile::find($id);


        $provider->update([
            'verified_at' => now(),
            'status' => 'approved'
        ]);

        // TODO: Add assign service provider role to user

        return to_route('admin.service-provider.index')->with('message_success', 'You have approved the application.');
    }
}
