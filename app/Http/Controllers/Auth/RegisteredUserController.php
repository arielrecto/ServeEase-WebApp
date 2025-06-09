<?php

namespace App\Http\Controllers\Auth;

use App\Models\Page;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Enums\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $termsAndCondition = Page::where('slug', 'terms-and-conditions')->firstOrFail();

        return Inertia::render('Auth/Register', compact(['termsAndCondition']));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->symbols()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);


        $customerRole = Role::where('name', UserRoles::CUSTOMER->value)->first();

        $user->assignRole($customerRole);

        return redirect(RouteServiceProvider::HOME);
    }
}
