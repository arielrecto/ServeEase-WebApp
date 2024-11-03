<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\Barangay;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function redirectAuthUser()
    {
        $brgys = Barangay::orderBy('name')->get();
        $services = ServiceType::orderBy('name')->get();
        return Inertia::render('Users/Search', compact(['brgys', 'services']));
    }

    // TODO: Add filtering logic
    public function servicesFilter(Request $request)
    {
        $services = Service::all();

        if ($request->byRating) {

        }

        if ($request->byPrice) {

        }

        return response()->json(['services' => $services]);
    }
}
