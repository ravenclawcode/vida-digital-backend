<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            $table->enum('status', ['taken', 'skipped', 'missed'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medication_logs_status', function (Blueprint $table) {
            //
        });
    }
};
