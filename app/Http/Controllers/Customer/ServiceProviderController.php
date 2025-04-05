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
        $providerProfile = auth()->user()->profile->providerProfile;

        if ($providerProfile) {
            $providerProfile->load(['serviceType', 'remarks.user']);
        }

        return Inertia::render('Users/Customer/ServiceProvider/Create', [
            'serviceTypes' => $serviceTypes,
            'providerProfile' => $providerProfile
        ]);
    }


    public function store(Request $request)
    {

        $request->validate([
            'service' => 'required',
            'experience' => 'required',
            'experience_duration' => 'required',
            'contact' => 'required',
            'valid_id_type' => 'required|string',
            'citizenship_document_type' => 'required|string',
            'valid_id_image' => 'required|mimes:png,jpg',
            'proof_document_image' => 'required|mimes:png,jpg',
            'citizenship_document_image' => 'required|mimes:png,jpg',
        ]);

        $validIdImageName = 'valid-id-' . uniqid() . '.' . $request->valid_id_image->extension();
        $validIdImage = $request->valid_id_image->storeAs('/provider/valid-id', $validIdImageName, 'public');

        $proofDocumentImageName = 'proof-document-' . uniqid() . '.' . $request->proof_document_image->extension();
        $proofDocumentImage = $request->proof_document_image->storeAs('/provider/proof-document', $proofDocumentImageName, 'public');

        $citizenshipDocumentImageName = 'certificate-' . uniqid() . '.' . $request->citizenship_document_image->extension();
        $citizenshipDocumentImage = $request->citizenship_document_image->storeAs('/provider/certificate', $citizenshipDocumentImageName, 'public');

        ProviderProfile::create([
            'service_type_id' => $request->service,
            'experience' => $request->experience,
            'experience_duration' => $request->experience_duration,
            'contact' => $request->contact,
            'valid_id_type' => $request->valid_id_type,
            'valid_id_image' => asset('/storage/' . $validIdImage),
            'citizenship_document_type' => $request->citizenship_document_type,
            'citizenship_document_image' => asset('/storage/' . $citizenshipDocumentImage),
            'proof_document_image' => asset('/storage/' . $proofDocumentImage),
            'profile_id' => $request->user()->profile->id,
            // 'verified_at' => now()
        ]);


        return to_route('customer.service-provider.create')->with('message_success', 'Application submitted successfully.');

    }
}
