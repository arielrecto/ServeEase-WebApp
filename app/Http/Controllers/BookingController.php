<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Remark;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\ServiceCart;
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


        $availServices = AvailService::with(['service', 'service.user', 'service.user.profile', 'service.user.profile.providerProfile', 'serviceCart'])
            ->whereUserId(Auth::user()->id)
            ->when($filter, function ($query) use ($filter) {
                if ($filter === "completed") {
                    $query->whereStatus("completed");
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
                    'service_id' => $availService->service->id,
                    'name' => $availService->service->name,
                    'provider' => $availService->service->user->name,
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'service_cart_id' => $availService->serviceCart?->id ?? null,
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
            ->whereStatus('completed')
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

    public function showArchive()
    {
        $availServices = AvailService::with(['service.user.profile.providerProfile'])
            ->where('status', 'completed')
            ->where('user_id', Auth::user()->id)
            ->latest()
            ->paginate(20)
            ->through(function ($availService) {
                return [
                    'id' => $availService->id,
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
            ->whereStatus('completed')
            ->count();
        $reviewsCount = FeedBack::whereUserId(Auth::user()->id)->count();

        return Inertia::render('Users/Customer/Booking/Archive', compact(['availServices', 'latestBookingsCount', 'pendingBookingsCount', 'reviewsCount', 'finishedBookingsCount']));
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

    public function showCart($id)
    {
        $serviceCart = ServiceCart::with([
            'availServices.service',
            'availServices.availServiceRemarks.user',
            'remarks.user'
        ])->findOrFail($id);

        $availServices = $serviceCart->availServices()
            ->with(['service', 'availServiceRemarks.user'])
            ->get()
            ->map(function ($availService) {
                return [
                    'id' => $availService->id,
                    'name' => $availService->service->name,
                    'total_price' => $availService->total_price,
                    'status' => $availService->status,
                    'start_date' => $availService->start_date,
                    'end_date' => $availService->end_date,
                    'availServiceRemarks' => $availService->availServiceRemarks
                ];
            });

        return Inertia::render('Users/Customer/Booking/ServiceCartDetail', [
            'serviceCart' => $serviceCart,
            'availServices' => $availServices
        ]);
    }

    public function reply(Request $request)
    {


        $request->validate([
            'content' => 'required|string',
            'remarkable_id' => 'required',
            'remarkable_type' => 'required|in:ServiceCart,AvailService',
        ]);

        $remark = new Remark([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        $remarkable = $request->remarkable_type === 'ServiceCart'
            ? ServiceCart::find($request->remarkable_id)
            : AvailService::find($request->remarkable_id);

        $remarkable->remarks()->save($remark);

        return back();
    }
}
