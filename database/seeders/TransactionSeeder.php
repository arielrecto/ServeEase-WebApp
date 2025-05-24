<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\AvailService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availServices = AvailService::with(['service.user'])->where('status', 'completed')->get();

        foreach ($availServices as $availService) {
            $amount = $availService->total_price;

            $transactionData = [
                'payment_account_id' => $availService->user->paymentAccounts->first()->id,
                'transaction_type' => 'payment',
                'amount' => $amount,
                'currency' => 'PHP',
                'transactionable_id' => $availService->id,
                'transactionable_type' => AvailService::class,
                'paid_by' => $availService->user_id,
                'paid_to' => $availService->service->user_id,
                'status' => 'approved',
            ];

            Transaction::create($transactionData);
        }
    }
}
