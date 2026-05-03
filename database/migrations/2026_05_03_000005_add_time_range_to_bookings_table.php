<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('time_start', 5)->nullable()->after('advertise_end'); // e.g. "08:00"
            $table->string('time_end', 5)->nullable()->after('time_start');      // e.g. "17:00"
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['time_start', 'time_end']);
        });
    }
};
