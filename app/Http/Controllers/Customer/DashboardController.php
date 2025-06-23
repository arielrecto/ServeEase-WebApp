<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\Barangay;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {

        $brgys = Barangay::orderBy('name')->get();
        $services = ServiceType::orderBy('name')->get();

        // $services = Service::with(['user.profile.providerProfile'])
        //     ->when($request->searchQuery, function ($q) use ($request) {

        //         $q->where('name', 'LIKE', "%{$request->searchQuery}%")
        //             ->orWhere(function ($q) use ($request) {
        //                 $q->whereHas('user.profile', function ($q) use ($request) {
        //                     $q->where('first_name', 'LIKE', "%{$request->searchQuery}%")
        //                         ->orWhere('last_name', 'LIKE', "%{$request->searchQuery}%");
        //                 });
        //             });
        //     })
        //     ->withCount('availService as avail_service_count')
        //     ->where('user_id', '!=', Auth::user()->id)
        //     ->latest()
        //     ->get();

        // $services = $services->sortBy(function ($service) {
        //     return -$service->avg_rate;
        // })->values();

        return Inertia::render('Users/Customer/Dashboard', compact(['services', 'brgys']));
    }
}
