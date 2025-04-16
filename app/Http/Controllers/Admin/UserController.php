<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Sex;
use App\Models\AvailService;
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
                $q->whereRelation('profile', 'first_name', 'LIKE', "%{$searchQuery}%")
                    ->orWhereRelation('profile', 'last_name', 'LIKE', "%{$searchQuery}%");
            })
            ->paginate(20)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->profile->full_name,
                    'is_suspended' => $user->is_suspended,
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


    public function show(User $user)
    {
        $user = User::with(['profile', 'roles'])->where('id', $user->id)->firstOrFail();
        $availServices = AvailService::with(['service', 'service.user.profile'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->toArray();

        return Inertia::render('Users/Admin/Users/Show', compact(['user', 'availServices']));
    }

    public function edit(User $user)
    {
        $profile = $user->profile;

        return Inertia::render('Users/Admin/Users/Edit', compact([
            'user',
            'profile'
        ]));
    }

    public function update(Request $request, User $user)
    {
        $safe = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['sometimes', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
        ]);

        $user->update(['name' => "{$safe['first_name']} {$safe['last_name']}"]);

        $user->profile->update($safe);

        return to_route('admin.users.index')->with('message_success', 'User information updated successfully');
    }

    public function confirm(Request $request, User $user)
    {
        $action = $request->action;
        return Inertia::render('Users/Admin/Users/Confirm', compact(['action', 'user']));
    }

    // TODO: Deactivate & activate
    public function destroy(User $user)
    {
        $user->update(['is_suspended' => true]);

        return back()->with('message_success', 'User account has been suspended.');
    }

    public function restore(User $user)
    {
        $user->update(['is_suspended' => false]);

        return back()->with('message_success', 'Account suspension has been revoked.');
    }
}
