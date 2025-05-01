<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::all()->collect();

        // dd($serviceTypes);

        return Inertia::render('Users/Customer/ServiceTypes/Index', compact(['serviceTypes']));
    }

    public function show(Request $request, ServiceType $serviceType)
    {
        $services = Service::with(['user.profile.providerProfile'])
            ->withCount(['availService as avail_service_count'])
            ->when($request->searchQuery, function ($query) use ($request, $serviceType) {
                $query->where('name', 'like', '%' . $request->searchQuery . '%')->where('service_type_id', $serviceType->id);
            })
            ->where('service_type_id', $serviceType->id)
            ->get();

        return Inertia::render('Users/Customer/ServiceTypes/Show', compact(['serviceType', 'services']));
    }
}
