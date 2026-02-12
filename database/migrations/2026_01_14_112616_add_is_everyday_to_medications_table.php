<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->boolean('is_everyday')->default(false)->after('reminder_time');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {});
    }
};
