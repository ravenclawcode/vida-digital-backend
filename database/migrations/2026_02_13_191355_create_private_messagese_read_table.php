<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->boolean('deleted_by_sender')->default(false);
            $table->boolean('deleted_by_receiver')->default(false);
            $table->boolean('is_deleted_everyone')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_messagese_read');
    }
};
