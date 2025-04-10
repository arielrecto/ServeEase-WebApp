<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\ServiceCart;
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
            ->whereDate('created_at', '>=', Carbon::now())
            ->whereIn('status', ['pending', 'in_progress'])
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

    public function bulkForm(Request $request, $provider_id)
    {
        $provider = User::findOrFail($provider_id);
        $services = Service::where('user_id', $provider_id)
            ->get();

        $initialService = Service::where('id', $request->input('query')['service_id'])->first();

        return Inertia::render('Users/Customer/Services/BulkForm', [
            'provider' => $provider,
            'services' => $services,
            'initialService' => $initialService
        ]);
    }

    public function bulkAvail(Request $request)
    {

        $request->validate([
            'services' => ['required', 'array', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'remark' => ['required', 'string'],
        ]);

        $services = Service::whereIn('id', $request->services)->get();
        $total_hours = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) * 8;


        $serviceChart = ServiceCart::create([
            'user_id' => Auth::user()->id,
            'reference_number' => strtoupper(uniqid('REF-')),
            'total_amount' => $request->total_price
        ]);



        try {
            foreach ($services as $service) {
                // Check if user is trying to avail their own service
                if ($service->user_id == Auth::user()->id) {
                    return response()->json(['message' => 'You cannot avail your own service'], 422);
                }

                // Calculate total price based on price type
                $total_price = $service->price;
                if ($service->price_type == 'hr') {
                    $total_price *= $total_hours;
                }

                // Create avail service record
                $availService = AvailService::create([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'remarks' => $request->remark,
                    'total_price' => $total_price,
                    'service_id' => $service->id,
                    'total_hours' => $total_hours,
                    'user_id' => Auth::user()->id,
                    'service_cart_id' => $serviceChart->id,
                ]);

                // Create notification for service provider
                // $notification = Notification::create([
                //     'user_id' => $service->user_id,
                //     'content' => GenerateNotificationAction::handle('booking', 'bulk-booking-created', Auth::user()),
                //     'type' => 'booking',
                //     'url' => "/service-provider/booking/{$availService->id}/detail"
                // ]);

                // broadcast(new NotificationSent($notification))->toOthers();
            }

            return to_route('customer.services.show', ['service' => $services->first()->id])
                ->with('message_success', 'Bulk service booking successful');
        } catch (\Exception $e) {
            return back()->with('message_error', $e->getMessage());
        }
    }
}
