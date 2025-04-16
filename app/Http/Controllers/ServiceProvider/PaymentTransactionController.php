<?php

namespace App\Http\Controllers\ServiceProvider;

use Inertia\Inertia;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentTransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['paymentAccount', 'paidBy', 'attachments', 'transactionable'])
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

        return back()->with('message', [
            'type' => 'success',
            'text' => "Payment {$request->status} successfully"
        ]);
    }
}
