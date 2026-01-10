<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('education_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('education_content_id')->constrained('education_contents')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'education_content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('education_likes');
    }
};