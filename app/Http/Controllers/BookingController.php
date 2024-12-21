<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->filter;
        $availServices = AvailService::with(['service', 'service.user', 'service.user.profile', 'service.user.profile.providerProfile'])
            ->whereUserId(Auth::user()->id)
            ->when($filter, function ($query) use ($filter) {
                if ($filter === "done") {
                    $query->whereStatus("done");
                }
                if ($filter === "pending") {
                    $query->whereStatus("pending");
                }
            })
            ->latest()
            ->paginate(20)
            ->through(function ($availService) {
                return [
                    'id' => $availService->id,
                    'hasFeedback' => $availService->feedback()->exists(),
                    'service_id' => $availService->service->id,
                    'name' => $availService->service->name,
                    'provider' => $availService->service->user->name,
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'created_at' => $availService->created_at
                ];
            });

        $weekStartDate = Carbon::now()->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d H:i');

        $latestBookingsCount = AvailService::whereUserId(Auth::user()->id)
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->count();
        $pendingBookingsCount = AvailService::whereUserId(Auth::user()->id)
            ->whereStatus('pending')
            ->count();
        $finishedBookingsCount = AvailService::whereUserId(Auth::user()->id)
            ->whereStatus('done')
            ->count();
        $reviewsCount = FeedBack::whereUserId(Auth::user()->id)->count();

        return Inertia::render("Users/Customer/Booking/Index", compact(['availServices', 'latestBookingsCount', 'pendingBookingsCount', 'reviewsCount', 'finishedBookingsCount']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::with(['user', 'user.profile', 'user.profile.providerProfile'])
            ->where('id', $id)
            ->first();

        return Inertia::render('Users/Customer/Booking/Show', compact(['service']));
    }

    public function detail(AvailService $availService)
    {
        $service = Service::with(['user', 'user.profile', 'user.profile.providerProfile'])
            ->where('id', $availService->service->id)
            ->first();

        return Inertia::render('Users/Customer/Booking/Detail', compact(['availService', 'service']));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
