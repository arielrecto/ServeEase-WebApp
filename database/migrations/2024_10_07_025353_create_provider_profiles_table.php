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
            $table->string('experience_duration');
            $table->string('experience');
            $table->string('contact');
            $table->string('valid_id_type');
            $table->string('valid_id_image');
            $table->string('citizenship_document_type');
            $table->string('citizenship_document_image');
            $table->string('proof_document_image');
            $table->dateTime('verified_at')->nullable();
            $table->string('status ')->default('pending');
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
