<?php

namespace App\Http\Controllers\ServiceProvider;

use Inertia\Inertia;
use App\Models\Remark;
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

class BookingController extends Controller
{
    public function index(Request $request)
    {

        $filter = $request->filter;
        $availServices = AvailService::with(['user.profile', 'service', 'service.user', 'serviceCart', 'service.user.profile', 'service.user.profile.providerProfile'])
            ->whereHas('service', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })
            ->when($filter, function ($query) use ($filter) {
                if ($filter === "completed") {
                    $query->whereStatus("completed");
                }
                if ($filter === "pending") {
                    $query->whereStatus("pending");
                }
            })
            ->whereNot('status', 'rejected')
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
                    'customer' => "{$availService->user->profile->first_name} {$availService->user->profile->last_name}",
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'reference_number' => $availService?->serviceCart?->reference_number,
                    'cart_id' => $availService->service_cart_id,
                    'created_at' => $availService->created_at
                ];
            });

        $weekStartDate = Carbon::now()->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d H:i');

        $query = AvailService::whereHas('service', function ($q) {
            $q->where('user_id', Auth::user()->id);
        });

        $latestBookingsCount = $query
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->count();
        $pendingBookingsCount = $query
            ->whereStatus('pending')
            ->count();
        $finishedBookingsCount = $query
            ->whereStatus('completed')
            ->count();
        $reviewsCount = FeedBack::whereUserId(Auth::user()->id)->count();


        return Inertia::render('Users/ServiceProvider/Booking/Index', compact(['availServices', 'latestBookingsCount', 'pendingBookingsCount', 'reviewsCount', 'finishedBookingsCount']));
    }

    public function detail(AvailService $availService)
    {


        $availService = AvailService::with(['service', 'service.user', 'serviceCart', 'service.user.profile', 'service.user.profile.providerProfile'])
            ->where('id', $availService->id)
            ->first();


        $service = Service::with(['user', 'user.profile', 'user.profile.providerProfile'])
            ->where('id', $availService->service->id)
            ->first();

        return Inertia::render('Users/ServiceProvider/Booking/Detail', compact(['availService', 'service']));
    }

    public function confirm(Request $request, AvailService $availService)
    {
        $status = $request->status;
        $id = $availService->id;

        return Inertia::render('Users/ServiceProvider/Booking/Confirm', compact(['status', 'id']));
    }

    public function updateStatus(Request $request, AvailService $availService)
    {
        // if (
        //     ($request->status === 'confirmed' ||
        //         $request->status === 'in_progress') &&
        //     AvailService::whereRelation(
        //         'service',
        //         'user_id',
        //         '=',
        //         Auth::user()->id
        //     )
        //         ->whereIn('status', ['in_progress'])
        //         ->where('service_cart_id', $availService->service_cart_id)
        //         ->first()?->service_cart_id !== $availService->service_cart_id
        // ) {
        //     return back()->with('message_error', 'You already have an ongoing booking!');
        // }

        $availService->update([
            'status' => $request->status
        ]);

        $message = '';

        switch ($availService->status) {
            case 'confirmed':
                $message = GenerateNotificationAction::handle('booking', 'booking-confirmed', $availService->service->user);
                break;
            case 'rejected':
                $message = GenerateNotificationAction::handle('booking', 'booking-rejected', $availService->service->user);
                break;
            case 'cancelled':
                $message = GenerateNotificationAction::handle('booking', 'booking-cancelled', $availService->service->user);
                break;
            case 'in_progress':
                $message = GenerateNotificationAction::handle('booking', 'booking-started', $availService->service->user);
                break;
            case 'completed':
                $message = GenerateNotificationAction::handle('booking', 'booking-completed', $availService->service->user);
                break;
        }

        $notification = Notification::create([
            'user_id' => $availService->user->id,
            'content' => $message,
            'type' => 'booking',
            'url' => "/customer/booking/{$availService->id}/detail"
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return back();
    }

    public function showCart(string $serviceCartId)
    {
        $serviceCart = ServiceCart::with([
            'user',
            'availServices.service',
            'availServices.availServiceRemarks.user',
            'remarks.user'
        ])
            ->where('id', $serviceCartId)
            ->first();

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
                    'availServiceRemarks' => $availService->availServiceRemarks,
                    'remarks' => $availService->remarks
                ];
            });

        return Inertia::render('Users/ServiceProvider/Booking/ServiceCartShow', [
            'serviceCart' => $serviceCart,
            'availServices' => $availServices
        ]);
    }

    public function approveAll(ServiceCart $serviceCart)
    {
        $availServices = $serviceCart->availServices()
            ->where('status', 'pending')
            ->get();

        foreach ($availServices as $service) {
            $service->update(['status' => 'confirmed']);

            // Create notification for customer
            $notification = Notification::create([
                'user_id' => $service->user_id,
                'content' => GenerateNotificationAction::handle('booking', 'booking-confirmed', auth()->user()),
                'type' => 'booking',
                'url' => "/customer/booking/{$service->id}/detail"
            ]);

            broadcast(new NotificationSent($notification))->toOthers();
        }

        return back()->with('message_success', 'All services have been approved');
    }

    public function rejectAll(ServiceCart $serviceCart)
    {
        $availServices = $serviceCart->availServices()
            ->where('status', 'pending')
            ->get();

        foreach ($availServices as $service) {
            $service->update(['status' => 'rejected']);

            // Create notification for customer
            $notification = Notification::create([
                'user_id' => $service->user_id,
                'content' => GenerateNotificationAction::handle('booking', 'booking-rejected', auth()->user()),
                'type' => 'booking',
                'url' => "/customer/booking/{$service->id}/detail"
            ]);

            broadcast(new NotificationSent($notification))->toOthers();
        }

        return back()->with('message_success', 'All services have been rejected');
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
            ? ServiceCart::findOrFail($request->remarkable_id)
            : AvailService::findOrFail($request->remarkable_id);

        $remarkable->remarks()->save($remark);

        return back();
    }
}
