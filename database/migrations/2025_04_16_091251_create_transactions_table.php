<?php

use App\Models\PaymentAccount;
use App\Models\User;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentAccount::class)->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // e.g., 'credit', 'debit'
            $table->decimal('amount', 15, 2);
            $table->morphs('transactionable'); // Polymorphic relation for different transaction types
            $table->string('currency')->default('PHP'); // Default currency
            $table->foreignIdFor(User::class, 'paid_by')->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(User::class, 'paid_to')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // e.g., 'pending', 'completed', 'failed'
            $table->string('reference_number')->unique()->nullable(); // Unique reference number for the transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
