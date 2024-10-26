<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceTypes = ServiceType::all()->collect();

        // dd($serviceTypes);

        return Inertia::render('Users/Admin/ServiceTypes/Index', compact(['serviceTypes']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Admin/ServiceTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $fields = $request->validate([
            'name' => ['required', 'max:60', 'unique:service_types,name'],
            'description' => ['required', 'max:255'],
            'thumbnail' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:5000'],
        ]);

        $imageName = 'thumbnail-' . uniqid() . '.' . $fields['thumbnail']->extension();
        $dir = $fields['thumbnail']->storeAs('/service_thumbnails', $imageName, 'public');

        $service = ServiceType::create([
            'name' => $fields['name'],
            'description' => $fields['description'],
            'thumbnail' => asset('/storage/' . $dir),
        ]);

        return back()->with(['message_success' => 'Service added successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = ServiceType::findOrFail($id);

        return Inertia::render('Users/Admin/ServiceTypes/Show', compact(['service']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = ServiceType::findOrFail($id);

        return Inertia::render('Users/Admin/ServiceTypes/Edit', compact(['service']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd('Updated', $request->all());

        $service = ServiceType::findOrFail($id);

        $fields = $request->validate([
            'name' => ['required', 'max:60', Rule::unique('service_types')->ignore($id)],
            'description' => ['required', 'max:255'],
            'thumbnail' => ['sometimes', 'file', 'mimes:png,jpg,jpeg', 'max:5000'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $imageName = 'thumbnail-' . uniqid() . '.' . $request->thumbnail->extension();
            $dir = $fields['thumbnail']->storeAs('/service_thumbnails', $imageName, 'public');

            $service->update(['thumbnail' => asset('/storage/' . $dir)]);
        }

        $service->update([
            'name' => $request->name ?? $service->name,
            'description' => $request->description ?? $service->description
        ]);

        return back()->with('message_success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // dd('Deleted');
        $service = ServiceType::findOrFail($id);

        $service->delete();

        return to_route('admin.service-types.index')->with('message_success', 'Service deleted successfully');
    }
}
