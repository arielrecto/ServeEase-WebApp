<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {

        $services = Service::with(['user'])->withCount('availService as avail_service_count')->where('user_id', '!=', Auth::user()->id)->latest()->get();

        return Inertia::render('Users/Customer/Dashboard', compact(['services']));
    }
}
