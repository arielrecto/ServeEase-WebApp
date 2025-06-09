<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
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
use App\Models\PaymentAccount;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateNotificationAction;

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
                    'service_id' => $availService->service?->id ?? 'N\A',
                    'name' => $availService->service?->name ?? 'N\A',
                    'provider' => $availService->service->user?->profile?->full_name ?? 'N\A',
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'service_cart_id' => $availService->serviceCart?->id ?? null,
                    'is_fully_paid' => $availService->is_fully_paid,
                    'has_feedback' => $availService->has_feedback,
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
        if ($availService->user_id !== Auth::user()->id) {
            return back()->with('message_error', 'You are not authorized to view this booking.');
        }

        $availService->load(['transactions.attachments', 'transactions.paymentAccount', 'attachments']);

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
                    'service_id' => $availService->service?->id ?? 'N\A',
                    'name' => $availService->service?->name ?? 'N\A',
                    'provider' => $availService->service->user?->profile?->full_name ?? 'N\A',
                    'status' => $availService->status,
                    'total_price' => $availService->total_price,
                    'service_cart_id' => $availService->serviceCart?->id ?? null,
                    'is_fully_paid' => $availService->is_fully_paid,
                    'has_feedback' => $availService->has_feedback,
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

        return Inertia::render('Users/Customer/Booking/Index', compact(['availServices', 'latestBookingsCount', 'pendingBookingsCount', 'reviewsCount', 'finishedBookingsCount']));
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

    public function showPayment(AvailService $availService)
    {
        if ($availService->user_id !== Auth::user()->id || ($availService->status !== 'confirmed' && $availService->status !== 'completed')) {
            return back()->with('message_error', 'You are not authorized to view this booking.');
        }

        if ($availService->is_fully_paid) {
            return back()->with('message_error', 'This booking is already fully paid.');
        }

        $service = $availService->service->load(['user', 'user.profile', 'user.profile.providerProfile']);
        // Get provider's payment accounts with better organization
        $paymentAccounts = PaymentAccount::where('user_id', $service->user_id)
            ->get()
            ->groupBy('account_type')
            ->map(function ($accounts) {
                return $accounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'account_name' => $account->account_name,
                        'account_number' => $account->account_number,
                        'account_type' => $account->account_type,
                        'is_primary' => $account->is_primary ?? false,
                        'provider_name' => $account->user->name
                    ];
                });
            });


        $transactionData = [
            'reference_prefix' => 'TXN-' . date('Ymd'),
            'amount' => $availService->total_price,
            'service_provider' => [
                'name' => $service->user->name,
                'id' => $service->user_id,
                'profile_photo' => $service->user->profile->user_avatar
            ],
            'service_details' => [
                'name' => $service->name,
                'id' => $service->id,
                'price_type' => $service->price_type,
                'duration' => [
                    'start' => $availService->start_date,
                    'end' => $availService->end_date
                ]
            ]
        ];

        return Inertia::render('Users/Customer/Booking/Payment', [
            'availService' => $availService,
            'service' => $service,
            'paymentAccounts' => $paymentAccounts,
            'transactionData' => $transactionData
        ]);
    }

    public function pay(Request $request)
    {
        $validationRules = [
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'transaction_type' => 'required|in:payment,deposit,reservation',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'transactionable_id' => 'required',
            'transactionable_type' => 'required|in:AvailService'
        ];

        // Get payment account to check if it's cash payment
        $paymentAccount = PaymentAccount::findOrFail($request->payment_account_id);

        // Add reference_number validation only for non-cash payments
        if ($paymentAccount->account_type !== 'cash') {
            $validationRules['reference_number'] = 'required|string|unique:transactions,reference_number';
            $validationRules['attachments.*'] = 'required|file|max:5120|mimes:jpg,jpeg,png,pdf';
        }

        $request->validate($validationRules);

        $availService = AvailService::findOrFail($request->transactionable_id);

        // Validate amount based on transaction type
        $minimumPayment = $request->transaction_type === 'deposit'
            ? ceil($availService->total_price * 0.3)
            : $availService->total_price;

        if ($request->amount < $minimumPayment && !$request->transaction_type === 'reservation') {
            return back()->withErrors([
                'amount' => "Minimum payment required is ₱{$minimumPayment}"
            ]);
        }

        if ($request->amount > $availService->total_price && $request->transaction_type !== 'reservation') {
            return back()->withErrors([
                'amount' => 'Amount cannot exceed the total price'
            ]);
        }



        // Create transaction record
        $transactionData = [
            'payment_account_id' => $request->payment_account_id,
            'transaction_type' => $request->transaction_type,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'transactionable_id' => $availService->id,
            'transactionable_type' => AvailService::class,
            'paid_by' => auth()->id(),
            'paid_to' => $availService->service->user_id,
            'status' => 'pending',
            'remaining_balance' => $availService->total_price - $request->amount
        ];

        // Only add reference number if provided (required for non-cash payments)
        if ($request->reference_number) {
            $transactionData['reference_number'] = $request->reference_number;
        }

        $transaction = Transaction::create($transactionData);

        // Only process attachments for non-cash payments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('payment-proofs', 'public');

                $transaction->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            }
        }

        // Send notification
        $message = '';

        if ($request->transaction_type === 'reservation') {
            $message = GenerateNotificationAction::handle(
                'payment',
                'payment-reservation',
                Auth::user(),
            );
        } else {
            $message = GenerateNotificationAction::handle(
                'payment',
                'payment-created',
                Auth::user(),
            );
        }

        $notification = Notification::create([
            'user_id' => $availService->service->user_id,
            'content' => $message,
            'type' => 'payment',
            'url' => "/service-provider/transactions"
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return redirect()->route('customer.booking.detail', $availService->id)
            ->with(
                'message_success',
                $request->transaction_type === 'deposit'
                ? 'Deposit payment submitted successfully'
                : 'Payment submitted successfully'
            );
    }

    public function confirmCancel(AvailService $availService)
    {
        return Inertia::render('Users/Customer/Booking/Cancel', [
            'availService' => $availService
        ]);
    }

    public function cancel(Request $request, AvailService $availService)
    {
        // Ensure the booking is in a cancellable state
        if (!in_array($availService->status, ['pending', 'confirmed'])) {
            return back()->withErrors([
                'message' => 'This booking cannot be canceled.'
            ]);
        }

        DB::transaction(function () use ($availService, $request) {
            // Update the status to "cancelled"
            $availService->update([
                'status' => 'cancelled',
            ]);

            // Create a remark if provided
            if ($request->has('remarks') && !empty($request->remarks)) {
                $availService->remarks()->create([
                    'content' => $request->remarks,
                    'user_id' => Auth::id(),
                ]);
            }

            $notification = Notification::create([
                'user_id' => $availService->service->user_id,
                'content' => GenerateNotificationAction::handle('booking', 'booking-cancelled', $availService->user, ['remark' => $request->remarks]),
                'type' => 'booking',
                'url' => "/customer/booking/{$availService->id}/detail"
            ]);

            broadcast(new NotificationSent($notification))->toOthers();
        });

        return back()->with('message_success', 'Booking has been canceled successfully.');
    }
}
