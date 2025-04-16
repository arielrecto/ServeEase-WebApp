<?php

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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // Polymorphic relation for different attachment types
            $table->string('file_name'); // Name of the file
            $table->string('file_path'); // Path to the file
            $table->string('file_type'); // Type of the file (e.g., 'image', 'document')
            $table->integer('file_size'); // Size of the file in bytes
            $table->string('mime_type'); // MIME type of the file (e.g., 'image/jpeg', 'application/pdf')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
