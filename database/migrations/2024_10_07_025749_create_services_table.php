<?php

use App\Models\User;
use App\Models\Barangay;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price');
            $table->string('thumbnail')->nullable();
            $table->tinyInteger('price_type')
                ->comment('fixed or duration');
            $table->longText('description');
            $table->longText('terms_and_conditions')->nullable();
            $table->boolean('is_approved');
            $table->boolean('is_quantifiable')->default(false);
            $table->integer('quantity')->default(1);
            $table->timestamp('archived_at')->nullable();
            $table->foreignIdFor(ServiceType::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Barangay::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
