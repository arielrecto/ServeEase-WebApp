<?php

use App\Models\Profile;
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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ServiceType::class)->constrained()->cascadeOnDelete();
            $table->string('experience');
            $table->string('contact');
            $table->dateTime('verified_at')->nullable();
            $table->string('certificate');
            $table->foreignIdFor(Profile::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
