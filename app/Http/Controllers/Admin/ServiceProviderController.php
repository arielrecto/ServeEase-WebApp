<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Enums\UserRoles;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use NunoMaduro\Collision\Provider;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $providers = ProviderProfile::whereNotNull('verified_at')
            ->with(['profile.user'])
            ->latest()
            ->when($search, function ($query) use ($search) {
                $query->with([
                    'profile' => function ($query) use ($search) {
                        $query->whereRelation('user', 'name', $search);
                    }
                ]);
            })
            ->paginate(20)
            ->through(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->profile->user->name,
                    'service' => ucwords($provider->service_type),
                    'experience' => $provider->experience
                ];
            });

        return Inertia::render('Users/Admin/ServiceProviders/Index', compact(['providers']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $providerProfile = ProviderProfile::with('serviceType:id,name')->where('id', $id)->first();
        // dd($providerProfile->profile);
        $user = $providerProfile->profile->user;
        $profile = $providerProfile->profile;
        $service = null;
        $bookingsCount = 0;
        $finishedBookingsCount = 0;
        $feedbackCount = 0;

        if ($user->services()->exists()) {
            $service = Service::with(['user', 'availService'])
                ->withCount([
                    'availService as bookings_count',
                    'availService as finished_bookings_count' => function ($query) {
                        $query->whereStatus('done');
                    }
                ])
                ->whereUserId($user->id)
                ->first();

            $availServices = AvailService::whereServiceId($service->id);
            $bookingsCount = $availServices->count();
            $finishedBookingsCount = $availServices->whereStatus("done")->count();
            $feedbackCount = FeedBack::whereRelation('availService', 'service_id', $service->id)->count();
        }

        return Inertia::render('Users/Admin/ServiceProviders/Show', compact(['user', 'profile', 'providerProfile', 'feedbackCount', 'bookingsCount', 'finishedBookingsCount', 'service']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    // public function delete(string $id)
    // {
    //     $providerProfile = ProviderProfile::find($id);

    //     return Inertia::render('Users/Admin/ServiceProviders/Delete', compact(['providerProfile']));
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }

    public function getProviderReviews(AvailService $availService)
    {
        $feedbacks = $availService->feedback;
        return Inertia::render('Users/Admin/Feedback/Provider', compact(['availService', 'feedbacks']));
    }
}
