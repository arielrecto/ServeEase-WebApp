<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Service;
use App\Enums\ServicesType;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use App\Http\Controllers\Controller;

class ServiceProviderController extends Controller
{
    public function create()
    {
        $serviceTypes = ServiceType::all();

        $service = transform(
            ProviderProfile::where('profile_id', auth()->user()->profile->id)->first(),
            fn() => ProviderProfile::where('profile_id', auth()->user()->profile->id)->first(),
            null
        );

        return Inertia::render('Users/Customer/ServiceProvider/Create', compact(['serviceTypes', 'service']));
    }


    public function store(Request $request)
    {
        // dd($request->all());


        $request->validate([
            'service' => 'required',
            'experience' => 'required',
            'contact' => 'required',
            'certificate' => 'required|mimes:png,jpg'
        ]);


        $imageName = 'certificate-' . uniqid() . '.' . $request->certificate->extension();
        $dir = $request->certificate->storeAs('/certificate', $imageName, 'public');



        ProviderProfile::create([
            'service_type_id' => $request->service,
            'experience' => $request->experience,
            'contact' => $request->contact,
            'certificate' => asset('/storage/' . $dir),
            'profile_id' => $request->user()->profile->id,
            // 'verified_at' => now()
        ]);


        return to_route('customer.service-provider.create')->with('message_success', 'Application submitted successfully.');

    }
}
