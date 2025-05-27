<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Models\AvailService;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Events\NotificationSent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateNotificationAction;

class PaymentTransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['paymentAccount', 'paidBy.profile', 'attachments', 'transactionable'])
            ->where('paid_to', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Users/ServiceProvider/Transactions/Index', [
            'transactions' => $transactions
        ]);
    }

    public function updateStatus(Transaction $transaction, Request $request)
    {


        $transaction->update([
            'status' => $request->status,
        ]);

        // Update the avail service status if this is approved
        if ($request->status === 'approved') {
            $availService = $transaction->transactionable;
            $availService->update([
                'payment_status' => $transaction->transaction_type === 'deposit' ? 'partial' : 'paid',
                'total_paid' => $availService->total_paid + $transaction->amount,
                'remaining_balance' => $availService->total_price - ($availService->total_paid + $transaction->amount),
                'last_payment_date' => now()
            ]);
        }

        // Send notification
        $message = '';

        if ($request->status === 'approved') {
            $message = GenerateNotificationAction::handle(
                'payment',
                'payment-approved',
                Auth::user(),
            );
        } else {
            $message = GenerateNotificationAction::handle(
                'payment',
                'payment-rejected',
                Auth::user(),
            );
        }

        $availService = AvailService::findOrFail($transaction->transactionable_id);

        $notification = Notification::create([
            'user_id' => $availService->user_id,
            'content' => $message,
            'type' => 'payment',
            'url' => "/customer/booking/{$availService->id}/detail"
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return back()->with('message', [
            'type' => 'success',
            'text' => "Payment {$request->status} successfully"
        ]);
    }
}
