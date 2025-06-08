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
        $search = $request->searchQuery;
        $providers = ProviderProfile::whereNotNull('verified_at')
            ->with(['profile.user', 'serviceType'])
            ->latest()
            ->when($search, function ($query) use ($search) {
                $query->whereRelation('profile', 'first_name', 'like', "%{$search}%")
                    ->orWhereRelation('profile', 'last_name', 'like', "%{$search}%");
            })
            ->paginate(20)
            ->through(function ($provider) {
                return [
                    'id' => $provider->id,
                    'user' => $provider->profile->user,
                    'name' => $provider->profile->full_name,
                    'service' => ucwords($provider->serviceType->name),
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
        $user = User::findOrFail($id);
        $profile = $user->profile;
        $services = Service::with(['feedbacks'])
            ->withCount([
                'availService as bookings_count',
                'availService as finished_bookings_count' => function ($query) {
                    $query->whereStatus('completed');
                }
            ])
            ->where('user_id', $user->id)
            ->get();
        $providerProfile = ProviderProfile::with('serviceType')->where('profile_id', $user->profile->id)->firstOrFail();
        $feedbacks = FeedBack::with(['user.profile'])
            ->whereHas('availService', function ($query) use ($services) {
                $query->whereIn('service_id', $services->map(fn($service) => $service->id)->toArray());
            })
            ->get();

        $finishedBookingCount = $services->sum('finished_bookings_count');
        $totalBookingCount = $services->sum('bookings_count');

        return Inertia::render('Users/Admin/ServiceProviders/Show', compact(['user', 'profile', 'providerProfile', 'services', 'feedbacks', 'finishedBookingCount', 'totalBookingCount']));
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
