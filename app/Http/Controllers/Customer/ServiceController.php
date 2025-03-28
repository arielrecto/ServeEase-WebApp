<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\AvailService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Events\NotificationSent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateNotificationAction;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $service = Service::with(['user', 'user.profile'])->where('id', $id)->first();

        $availServices = AvailService::with(['service', 'service.user', 'service.user.profile', 'service.user.profile.providerProfile'])
            ->whereRelation(
                'service',
                'user_id',
                $service->user->id
            )
            ->whereDate('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('status', 'pending')
            ->orwhere('status', 'in_progress')
            ->get()
            ->toArray();

        return Inertia::render('Users/Customer/Services/Show', compact(['service', 'availServices']));
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

    public function availCreate(string $id)
    {
        $service = Service::with(['user'])->where('id', $id)->first();



        return Inertia::render('Users/Customer/Services/Avail', compact(['service']));
    }

    public function availStore(Request $request)
    {

        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'remark' => 'required|string',
            'total' => 'required|numeric',
            'service' => 'required|exists:services,id'
        ]);

        $service = Service::find($request->service);

        if ($service->user_id == Auth::user()->id) {
            return back()->with(['message_error' => 'You cannot avail your own service']);
        }



        $total_hours = Carbon::parse($request->startDate)->diffInDays(Carbon::parse($request->endDate)) * 8;


        if ($service->price_type == 'hr') {
            $total_hours = $request->hours;
        }


        $availService = AvailService::create([
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'remarks' => $request->remark,
            'total_price' => $request->total,
            'service_id' => $request->service,
            'total_hours' => $total_hours,
            'user_id' => Auth::user()->id
        ]);

        $notification = Notification::create([
            'user_id' => $availService->service->user->id,
            'content' => GenerateNotificationAction::handle('booking', 'booking-created', Auth::user()),
            'type' => 'booking',
            'url' => "/service-provider/booking/{$availService->id}/detail"
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return to_route('customer.services.show', ['service' => $request->service])->with(['message_success']);
    }

    public function getFeedbackByService(Request $request, Service $service)
    {
        $rate = $request->rating;

        $feedbacks = FeedBack::with(['user', 'user.profile'])
            ->whereHas('availService', function ($query) use ($service) {
                $query->whereServiceId($service->id);
            })
            ->when($rate, function ($query) use ($rate) {
                $query->whereRate($rate);
            })
            ->latest()
            ->get();

        return response()->json($feedbacks, 200);
    }
}
