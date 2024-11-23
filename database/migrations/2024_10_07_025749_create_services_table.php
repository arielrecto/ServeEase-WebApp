<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->tinyInteger('service_type');
            $table->tinyInteger('barangay_id');
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
