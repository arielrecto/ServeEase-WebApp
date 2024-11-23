<?php

namespace App\Http\Controllers;

use App\Enums\ServicesType;
use Inertia\Inertia;
use App\Models\Barangay;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class GuestController extends Controller
{
    public function search()
    {
        $types = ServiceType::orderBy('name')->get();
        $canLogin = Route::has('login');
        $canRegister = Route::has('register');
        $brgys = Barangay::orderBy('name')->get();
        $services = ServiceType::orderBy('name')->get();
        return Inertia::render('Guest/Search', compact(['brgys', 'types', 'services', 'canLogin', 'canRegister']));
    }

    public function services()
    {
        $canLogin = Route::has('login');
        $canRegister = Route::has('register');
        $services = ServiceType::orderBy('name')->get();
        return Inertia::render('Guest/Services', compact(['services', 'canLogin', 'canRegister']));
    }

    public function show(string $id)
    {
        $canLogin = Route::has('login');
        $canRegister = Route::has('register');
        $services = ServiceType::orderBy('name')->get();
        $service = ServiceType::findOrFail($id);
        return Inertia::render('Guest/Show', compact(['service', 'canLogin', 'canRegister']));
    }
}
