<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Remark;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\ServiceCart;
use App\Models\AvailService;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\PersonalEvent;
use Illuminate\Support\Carbon;
use App\Events\NotificationSent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateNotificationAction;
use Faker\Provider\ar_EG\Person;

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
        $service = Service::with(['user.profile.providerProfile'])->where('id', $id)->first();

        $totalUserServices = Service::where('user_id', $service->user->id)->count();

        $availServices = AvailService::with(['user.profile', 'service', 'service.user', 'service.user.profile', 'service.user.profile.providerProfile'])
            ->whereRelation(
                'service',
                'user_id',
                $service->user->id
            )
            ->whereDate('created_at', '>=', Carbon::now())
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->get()
            ->toArray();

        $personalEvents = PersonalEvent::where('user_id', $service->user->id)
            ->orderBy('start_date')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->event_name,
                    'start' => $event->start_date,
                    'end' => $event->end_date,
                    'type' => $event->event_type,
                    'description' => $event->description
                ];
            });


        $ongoingBookingsCount = AvailService::with(['service.user.profile'])
            ->whereRelation(
                'service',
                'user_id',
                $service->user->id
            )
            ->where('status', 'in_progress')
            ->count();

        return Inertia::render('Users/Customer/Services/Show', compact(['service', 'availServices', 'totalUserServices', 'ongoingBookingsCount', 'personalEvents']));
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
        $service = Service::active()
            ->with(['user'])
            ->where('id', $id)
            ->first();

        if (!$service) {
            abort(404);
        }

        $personalEvents = PersonalEvent::where('user_id', $service->user->id)
            ->orderBy('start_date')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->event_name,
                    'start' => $event->start_date,
                    'end' => $event->end_date,
                    'type' => $event->event_type,
                    'description' => $event->description
                ];
            });

        return Inertia::render('Users/Customer/Services/Avail', compact(['service', 'personalEvents']));
    }

    public function availStore(Request $request)
    {


        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'remark' => 'nullable|sometimes|string',
            'total' => 'required|numeric',
            'service' => 'required|exists:services,id',
            'attachments.*' => 'nullable|file|max:10240',

        ]);

        $service = Service::find($request->service);

        if (!$service->canBeBooked()) {
            return back()->with(['message_error' => 'This service is no longer available for booking']);
        }

        if ($service->user_id == Auth::user()->id) {
            return back()->with(['message_error' => 'You cannot avail your own service']);
        }

        if (
            PersonalEvent::where('user_id', $service->user->id)
            ->where('start_date', '<=', $request->endDate)
            ->where('end_date', '>=', $request->startDate)
            ->exists()
        ) {
            return back()->with(['message_error' => 'Service Provider have a personal event during this time']);
        }

        $total_hours = Carbon::parse($request->startDate)->diffInDays(Carbon::parse($request->endDate)) * 8;

        if ($service->price_type == 'hr') {
            $total_hours = $request->hours;
        }

        // Create avail service with optional time
        $availService = AvailService::create([
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'start_time' => $request->startTime ?? null,
            'end_time' => $request->endTime ?? null,
            'remarks' => $request->remark ?? "No additional note.",
            'total_price' => $request->total,
            'service_id' => $request->service,
            'total_hours' => $total_hours,
            'user_id' => Auth::user()->id,
            'quantity' => $request->quantity ?? 1,
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/avail-services', 'public');

                $availService->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        $notification = Notification::create([
            'user_id' => $availService->service->user->id,
            'content' => GenerateNotificationAction::handle('booking', 'booking-created', Auth::user()),
            'type' => 'booking',
            'url' => "/service-provider/booking/{$availService->id}/detail"
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return to_route('customer.booking.detail', ['availService' => $availService])
            ->with(['message_success' => 'Service booked successfully']);
    }

    public function getFeedbackByService(Request $request, Service $service)
    {
        $rate = $request->rating;

        $feedbacks = FeedBack::with(['user', 'user.profile', 'attachments'])
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
        $services = Service::active()
            ->where('user_id', $provider_id)
            ->get();

        $initialService = Service::active()
            ->where('id', $request->input('query')['service_id'])
            ->first();

        if (!$initialService) {
            abort(404);
        }

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
            'start_time' => ['required'],
            'end_time' => ['required'],
            'remark' => ['required', 'string'],
            'includeTime' => ['boolean'],
        ]);

        $services = Service::whereIn('id', $request->services)->get();
        $total_hours = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) * 8;


        $serviceChart = ServiceCart::create([
            'user_id' => Auth::user()->id,
            'reference_number' => strtoupper(uniqid('REF-')),
            'total_amount' => $request->total_amount
        ]);


        Remark::create([
            'content' => $request->remark,
            'user_id' => Auth::user()->id,
            'remarkable_id' => $serviceChart->id,
            'remarkable_type' => ServiceCart::class
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

                if (count($request->serviceDetails) > 0) {
                    $total_price = $request->serviceDetails[$service->id]['bargain_price'];
                }





                // Create avail service record with time if included
                $availService = AvailService::create([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'start_time' => $request->start_time ?? null,
                    'end_time' =>  $request->end_time ?? null,
                    'remarks' => $request->remark,
                    'total_price' => $total_price,
                    'service_id' => $service->id,
                    'total_hours' => $total_hours,
                    'user_id' => Auth::user()->id,
                    'service_cart_id' => $serviceChart->id,
                ]);

                Remark::create([
                    'content' => $request->serviceDetails[$service->id]['remark'],
                    'user_id' => Auth::user()->id,
                    'remarkable_id' => $availService->id,
                    'remarkable_type' => AvailService::class
                ]);
            }

            return to_route('customer.services.show', ['service' => $services->first()->id])
                ->with('message_success', 'Bulk service booking successful');
        } catch (\Exception $e) {
            return back()->with('message_error', $e->getMessage());
        }
    }
}
