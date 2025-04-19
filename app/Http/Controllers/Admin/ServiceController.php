<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Barangay;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        $user = User::findOrFail($service->user->id);
        $serviceTypes = ServiceType::get();
        $barangays = Barangay::get();

        return Inertia::render('Users/Admin/Services/Edit', compact(['service', 'serviceTypes', 'barangays', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $fields = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'price_type' => 'required',
            'description' => 'required',
            'terms_and_conditions' => 'required',
            'barangay_id' => 'required|exists:barangays,id',
            'thumbnail' => 'nullable|sometimes|file|mimes:png,jpg,jpeg|max:5000',
        ]);

        if ($request->hasFile('thumbnail')) {
            $imageName = 'thumbnail-' . uniqid() . '.' . $request->thumbnail->extension();
            $dir = $request->thumbnail->storeAs('/service_thumbnails', $imageName, 'public');
            $fields['thumbnail'] = asset('/storage/' . $dir);
        }

        $service->update($fields);

        return to_route('admin.service-provider.index')->with('message_success', 'Service updated successfully.');
    }

    public function delete(string $id)
    {
        $service = Service::findOrFail($id);

        return Inertia::render('Users/Admin/Services/Delete', compact(['service']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        $service->delete();

        return back()->with('message_success', 'Service has been deleted.');
    }
}
