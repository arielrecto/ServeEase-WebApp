<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->boolean('is_seen')->default(false);
            $table->foreignIdFor(Conversation::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class, 'sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(User::class, 'receiver_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
