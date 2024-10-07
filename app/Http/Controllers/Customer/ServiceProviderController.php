<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function create(){

    }


    public function store(Request $request){


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

    }
}
