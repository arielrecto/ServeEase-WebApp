<?php

namespace App\Http\Controllers;

use App\Enums\Sex;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Profile;
use App\Models\Service;
use App\Models\FeedBack;
use App\Enums\ServicesType;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $sexChoices = Sex::cases();
        $profile = $request->user()?->profile;
        $providerProfile = $profile?->providerProfile;
        $serviceTypes = ServicesType::cases();

        // dd($providerProfile);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'sexChoices' => $sexChoices,
            'serviceTypes' => $serviceTypes,
            'profile' => $profile,
            'providerProfile' => $providerProfile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateProviderProfile(Request $request)
    {
        // TODO: Implement update feature for user's provider profile

        dd($request->all());
    }

    public function provider(Request $request)
    {
        if (!Auth::user()->profile->providerProfile()->whereNotNull('verified_at')->exists()) {
            return back();
        }

        $user = Auth::user();
        $profile = $user->profile;
        $service = Service::with(['user', 'availService'])
            ->withCount([
                'availService as bookings_count',
                'availService as finished_bookings_count' => function ($query) {
                    $query->whereStatus('completed');
                }
            ])
            ->whereUserId($user->id)
            ->first();
        $providerProfile = $user->profile->providerProfile;
        $feedbackCount = FeedBack::when($service, function ($query) {
            $query->whereHas('availService', function ($query) use ($service) {
                $query->whereServiceId($service->id);
            });
        })->count();

        // dd($service, $feedbackCount);

        return Inertia::render('Profile/Provider', compact(['user', 'profile', 'providerProfile', 'feedbackCount', 'service']));
    }


    public function showProviderProfile(Request $request, ProviderProfile $providerProfile)
    {
        $user = $providerProfile->profile->user;
        $profile = $providerProfile->profile;
        $service = Service::with(['user', 'availService'])
            ->withCount([
                'availService as bookings_count',
                'availService as finished_bookings_count' => function ($query) {
                    $query->whereStatus('completed');
                }
            ])
            ->whereUserId($user->id)
            ->first();
        $feedbackCount = FeedBack::whereHas('availService', function ($query) use ($service) {
            $query->whereServiceId($service->id);
        })->count();

        return Inertia::render('Profile/Provider', compact(['user', 'profile', 'providerProfile', 'feedbackCount', 'service']));
    }

    public function updateProfile(Request $request)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            $profile = Profile::create([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'gender' => $request->gender,
                'address' => $request->address,
                'user_id' => $request->user()->id
            ]);



            if ($request->hasFile('avatar')) {

                $imageName = 'avatar-' . uniqid() . '.' . $request->avatar->extension();
                $dir = $request->avatar->storeAs('/profile', $imageName, 'public');

                $profile->update([
                    'avatar' => asset('/storage/' . $dir),
                ]);
            }


            return back()->with(['message_success' => 'profile Information Added!']);
        }


        $profile->update([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);


        if ($request->hasFile('avatar')) {

            $imageName = 'avatar-' . uniqid() . '.' . $request->avatar->extension();
            $dir = $request->avatar->storeAs('/profile', $imageName, 'public');

            $profile->update([
                'avatar' => asset('/storage/' . $dir),
            ]);
        }



        return back()->with(['message_success' => 'Profile Information Updated!']);
    }
}
