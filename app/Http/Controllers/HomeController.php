<?php

namespace App\Http\Controllers;

use App\Enums\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function index()
    {
        $authUser = Auth::user();

        $role = $authUser->roles()->first()->name;

        switch ($role) {
            case UserRoles::ADMIN->value:
                return to_route('admin.dashboard');
                break;
            case UserRoles::CUSTOMER->value:
                return to_route('customer.dashboard');
                break;
            default:
                return to_route('welcome');
                break;
        }
    }
}
