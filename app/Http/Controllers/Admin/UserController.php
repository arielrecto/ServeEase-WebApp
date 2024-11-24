<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Enums\UserRoles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $userRole = $request->role;
        $searchQuery = $request->searchQuery;

        // dd($searchQuery);

        $customerCount = User::with(['roles', 'profile'])
            ->has('profile')
            ->whereHas("roles", function ($q) {
                $q->where("name", "customer");
            })
            ->count();

        $providerCount = User::whereHas('profile', function ($q) {
            $q->whereHas('providerProfile', function ($providerQuery) {
                $providerQuery->whereNot('verified_at', null);
            });
        })->count();

        $users = User::with(['roles', 'profile'])
            ->has('profile')
            // ->whereHas("roles", function ($q) {
            //     $q->whereIn("name", ["customer", "service provider"]);
            // })
            ->when($userRole, function ($q) use ($userRole) {
                if ($userRole === 'customers') {
                    $q->has('profile')
                        ->whereHas("roles", function ($roleQuery) {
                            $roleQuery->where("name", "customer");
                        });
                } else if ($userRole === 'providers') {
                    $q->whereHas('profile', function ($profileQuery) {
                        $profileQuery->whereHas('providerProfile', function ($providerQuery) {
                            $providerQuery->whereNot('verified_at', null);
                        });
                    });
                }
            })
            ->when($searchQuery, function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%');
            })
            ->orderBy('name', 'ASC')
            ->paginate(20)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'created_at' => $user->created_at
                ];
            });

        // dd($users);

        $userCount = User::with(['roles', 'profile'])
            ->has('profile')
            ->count();

        return Inertia::render('Users/Admin/Users/Index', compact(['users', 'userCount', 'customerCount', 'providerCount']));
    }

    public function getUsers(Request $request)
    {
        $userRole = $request->role;
        $users = null;

        $users = User::with(['roles', 'profile'])
            ->has('profile')
            ->when($userRole, function ($q) use ($userRole) {

            })
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["customer", "service provider"]);
            })
            ->get();

        // if ($userRole === 'customers') {
        //     $users = User::with(['roles.name', 'profile'])
        //         ->has('profile')
        //         ->whereHas('roles', function ($q) {
        //             $q->whereIn('name', ['customer']);
        //         })
        //         ->get();
        // } else if ($userRole === 'providers') {
        //     $users = User::with(['roles.name', 'profile'])
        //         ->has('profile')
        //         ->whereHas('roles', function ($q) {
        //             $q->whereIn('name', ['service provider']);
        //         })
        //         ->get();
        // }

        return response()->json(['users' => $users]);
    }


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }
}
