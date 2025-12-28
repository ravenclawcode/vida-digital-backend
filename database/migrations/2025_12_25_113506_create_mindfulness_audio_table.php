<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
        Schema::create('mindfulness_audios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('category'); // Relaksasi, Meditasi, atau Tidur
            $table->text('description');
            $table->string('duration'); // Contoh: 5:00
            $table->string('audio_url'); // Link file audio
            $table->string('cover_url')->nullable(); // Gambar cover
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mindfulness_audio');
    }
};
