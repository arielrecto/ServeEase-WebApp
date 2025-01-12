<?php

namespace App\Http\Controllers\ServiceProvider;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public  function  index(Request $request)
    {

        $filter = $request->filter;
        $availServices = AvailService::with(['service', 'service.user', 'service.user.profile', 'service.user.profile.providerProfile'])
            ->whereHas('service', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })
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
                    'service_id' => $availService->service->id,
                    'name' => $availService->service->name,
                    'start_date' => $availService->start_date,
                    'end_date' => $availService->end_date,
                    'provider' => $availService->service->user->name,
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'created_at' => $availService->created_at
                ];
            });

        $weekStartDate = Carbon::now()->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d H:i');

        $query =  AvailService::whereHas('service', function ($q) {
            $q->where('user_id', Auth::user()->id);
        });

        $latestBookingsCount = $query
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->count();
        $pendingBookingsCount = $query
            ->whereStatus('pending')
            ->count();
        $finishedBookingsCount = $query
            ->whereStatus('done')
            ->count();
        $reviewsCount = FeedBack::whereUserId(Auth::user()->id)->count();


        return Inertia::render('Users/ServiceProvider/Booking/Index', compact(['availServices', 'latestBookingsCount', 'pendingBookingsCount', 'reviewsCount', 'finishedBookingsCount']));
    }

    public function detail(AvailService $availService)
    {
        $service = Service::with(['user', 'user.profile', 'user.profile.providerProfile'])
            ->where('id', $availService->service->id)
            ->first();

        return Inertia::render('Users/ServiceProvider/Booking/Detail', compact(['availService', 'service']));
    }
    public function updateStatus(Request $request, AvailService $availService)
    {

        $availService->update([
            'status' => $request->status
        ]);



        return back();
    }
}
