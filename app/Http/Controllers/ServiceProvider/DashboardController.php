<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\AvailService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {

        $user = Auth::user();

        $services = Service::where('user_id', $user->id)->get();

        $availServicePending = AvailService::where('status', 'pending')
        ->whereHas('service', function($q) use($user) {
            $q->where('user_id', $user->id);
        })->get();

        return Inertia::render('Users/ServiceProvider/Dashboard', compact(['services', 'availServicePending']));
    }
}
