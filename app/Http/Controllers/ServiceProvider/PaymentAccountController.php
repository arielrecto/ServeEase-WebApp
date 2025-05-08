<?php

namespace App\Http\Controllers\ServiceProvider;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\PaymentAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentAccountController extends Controller
{
    public function index()
    {
        $paymentAccounts = PaymentAccount::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'account_name' => $account->account_name,
                    'account_number' => $account->account_number,
                    'account_type' => $account->account_type,
                    'created_at' => $account->created_at
                ];
            });

        return Inertia::render('Users/ServiceProvider/PaymentAccount/Index', [
            'paymentAccounts' => $paymentAccounts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string|in:gcash,paymaya,bank,cash'
        ]);

        PaymentAccount::create([
            'user_id' => Auth::id(),
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type
        ]);

        return back()->with('message_success', 'Payment account added successfully');
    }

    public function update(Request $request, PaymentAccount $paymentAccount)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string|in:gcash,paymaya,bank,cash'
        ]);

        $paymentAccount->update([
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type
        ]);

        return back()->with('message_success', 'Payment account updated successfully');
    }

    public function destroy(PaymentAccount $paymentAccount)
    {
        $paymentAccount->delete();
        return back()->with('message_success', 'Payment account deleted successfully');
    }
}
