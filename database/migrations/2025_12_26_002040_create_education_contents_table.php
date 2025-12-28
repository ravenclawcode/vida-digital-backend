<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('education_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->enum('type', ['video', 'artikel']);
            $table->string('category');
            $table->string('duration');
            $table->text('description')->nullable(); // Untuk deskripsi video
            $table->string('video_url')->nullable(); // ID YouTube
            $table->longText('content')->nullable(); // Isi teks artikel
            $table->text('important_note')->nullable(); // Box pink catatan penting
            $table->string('thumbnail')->nullable(); // Auto dari YT
            $table->integer('likes')->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_contents');
    }
};
