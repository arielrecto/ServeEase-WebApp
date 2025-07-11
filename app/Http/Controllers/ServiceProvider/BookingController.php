<?php

namespace App\Http\Controllers\ServiceProvider;

use Inertia\Inertia;
use App\Models\Remark;
use App\Models\Service;
use App\Models\FeedBack;
use App\Models\ServiceCart;
use App\Models\Transaction;
use App\Models\AvailService;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\PersonalEvent;
use Illuminate\Support\Carbon;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateNotificationAction;

class BookingController extends Controller
{
    public function index(Request $request)
    {

        $personalEvents = PersonalEvent::where('user_id', Auth::id())
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
                if ($filter === "in_progress") {
                    $query->whereStatus("in_progress");
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
                    'address' => $availService->service->user->profile->address,
                    'customer' => "{$availService->user->profile->first_name} {$availService->user->profile->last_name}",
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'reference_number' => $availService?->serviceCart?->reference_number,
                    'cart_id' => $availService->service_cart_id,
                    'created_at' => $availService->created_at
                ];
            });

        // Replace the counting section with this optimized version
        $weekStartDate = Carbon::now()->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d H:i');

        // Create base query to avoid repetition
        $baseQuery = AvailService::whereRelation('service', 'user_id', '=', auth()->user()->id);

        // Get all counts in a single query using select subqueries
        $bookingCounts = $baseQuery->selectRaw('
            COUNT(*) as total_bookings,
            SUM(CASE
                WHEN created_at BETWEEN ? AND ? THEN 1
                ELSE 0
            END) as latest_bookings,
            SUM(CASE
                WHEN status = "in_progress" THEN 1
                ELSE 0
            END) as ongoing_bookings,
            SUM(CASE
                WHEN status = "pending" THEN 1
                ELSE 0
            END) as pending_bookings,
            SUM(CASE
                WHEN status = "completed" THEN 1
                ELSE 0
            END) as finished_bookings',
            [$weekStartDate, $weekEndDate]
        )->first();

        // Get reviews count
        $reviewsCount = FeedBack::whereUserId(Auth::user()->id)->count();

        // Extract values from the result
        $latestBookingsCount = $bookingCounts->latest_bookings;
        $ongoingBookingsCount = $bookingCounts->ongoing_bookings;
        $pendingBookingsCount = $bookingCounts->pending_bookings;
        $finishedBookingsCount = $bookingCounts->finished_bookings;


        return Inertia::render('Users/ServiceProvider/Booking/Index', compact(['availServices', 'latestBookingsCount', "ongoingBookingsCount", 'pendingBookingsCount', 'reviewsCount', 'finishedBookingsCount', 'personalEvents']));
    }

    public function detail(AvailService $availService)
    {


        $availService = AvailService::with(['service', 'service.user', 'serviceCart', 'service.user.profile', 'service.user.profile.providerProfile', 'attachments', 'remarks'])
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

        try {
            $availService->loadMissing('user', 'service.user');

            $message = match ($request->status) {
                'confirmed' => GenerateNotificationAction::handle('booking', 'booking-confirmed', $availService->service->user),
                'rejected' => GenerateNotificationAction::handle('booking', 'booking-rejected', $availService->service->user, [
                    'remark' => $request->remark,
                    'otherRemark' => $request->otherRemark ?? null
                ]),
                'cancelled' => GenerateNotificationAction::handle('booking', 'booking-cancelled', $availService->service->user),
                'in_progress' => GenerateNotificationAction::handle('booking', 'booking-started', $availService->service->user),
                'completed' => GenerateNotificationAction::handle('booking', 'booking-completed', $availService->service->user),
            };

            $payload = DB::transaction(function () use ($request, $availService, $message) {
                $availService->update([
                    'status' => $request->status
                ]);

                if ($request->status === 'rejected') {
                    $remarkContent = $request->remark;
                    if ($request->remark === 'Other' && !empty($request->otherRemark)) {
                        $remarkContent .= ': ' . $request->otherRemark;
                    }
                    $availService->remarks()->create([
                        'user_id' => Auth::id(),
                        'content' => $remarkContent
                    ]);
                }

                // dd($availService);

                $notification = Notification::create([
                    'user_id' => $availService->user->id,
                    'content' => $message,
                    'type' => 'booking',
                    'url' => $availService->status === "confirmed" ? "/customer/booking/payment/{$availService->id}" : "/customer/booking/{$availService->id}/detail"
                ]);

                return $notification;
            });

            broadcast(new NotificationSent($payload))->toOthers();

            return back();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('message_error', 'Oops! Something went wrong. Please try again.');
        }
    }

    public function markAsFullyPaid(AvailService $availService)
    {
        $notification = DB::transaction(function () use ($availService) {
            $amountPaid = Transaction::where('transactionable_type', AvailService::class)
                ->where('transactionable_id', $availService->id)
                ->where('status', 'approved')
                ->sum('amount');

            if ($amountPaid >= $availService->total_price) {
                return back()->with('message_error', 'This booking is already fully paid.');
            }

            $remainingAmount = $availService->total_price - $amountPaid;

            // dd($remainingAmount);

            $transaction = Transaction::create([
                'transactionable_id' => $availService->id,
                'transactionable_type' => AvailService::class,
                'paid_to' => Auth::user()->id,
                'paid_by' => $availService->user_id,
                'amount' => $remainingAmount,
                'status' => 'approved',
                'transaction_type' => 'cash',
                'reference_number' => 00000000,
                'currency' => 'PHP'
            ]);
            $message = GenerateNotificationAction::handle('booking', 'fully-paid', Auth::user());

            $notification = Notification::create([
                'user_id' => $availService->user_id,
                'content' => $message,
                'type' => 'booking',
                'url' => "/customer/booking/{$availService->id}/detail"
            ]);

            return $notification;
        });

        broadcast(new NotificationSent($notification))->toOthers();

        return back()->with('message_success', 'Successfully marked as fully paid.');
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
                'url' => "/customer/booking/payment/{$service->id}"
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
