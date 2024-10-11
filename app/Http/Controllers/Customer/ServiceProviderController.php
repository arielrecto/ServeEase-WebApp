<?php

namespace App\Http\Controllers\Customer;

use App\Enums\ServicesType;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use App\Http\Controllers\Controller;

class ServiceProviderController extends Controller
{
    public function create()
    {
        $serviceTypes = ServicesType::cases();

        return Inertia::render('Users/Customer/ServiceProvider/Create', compact(['serviceTypes']));
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
            'service_type' => $request->service,
            'experience' => $request->experience,
            'contact' => $request->contact,
            'certificate' => asset('/storage/' . $dir),
            'profile_id' => $request->user()->profile->id
        ]);


        return to_route('customer.service-provider.create')->with('message_success', 'Application submitted successfully.');

    }
}
