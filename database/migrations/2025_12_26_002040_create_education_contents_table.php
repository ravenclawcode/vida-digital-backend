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
            $table->longText('description');
            $table->string('video_url')->nullable();
            $table->text('important_note')->nullable();
            $table->string('thumbnail')->nullable();
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
