<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);


        return Inertia::render('Users/ServiceProvider/Services/Index', compact(['services']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $serviceTypes = ServiceType::get();

        $barangays = Barangay::get();

        return Inertia::render('Users/ServiceProvider/Services/Create', compact(['serviceTypes', 'barangays']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);


        $imageName = 'thumbnail-' . uniqid() . '.' . $request->thumbnail->extension();
        $dir = $request->thumbnail->storeAs('/service_thumbnails', $imageName, 'public');


        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'price_type' => $request->priceType,
            'terms_and_conditions' => $request->termAndCondition,
            'service_type' => $request->serviceType,
            'is_approved' => false,
            'barangay_id' => $request->barangay,
            'user_id' => $request->user()->id,
            'thumbnail' => asset('/storage/' . $dir)
        ]);



        return back()->with([
            'message_success' => 'Service Added'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::find($id);
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
        $service = Service::find($id);


        $service->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'user_id' => $request->user()->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);


        $service->delete();
    }
}
