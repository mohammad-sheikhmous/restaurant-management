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
        Schema::create('booking_policies', function (Blueprint $table) {
            $table->id();

            $table->time('advance_booking');
            $table->time('cancel_booking');
            $table->boolean('manual_confirmation')->default('1');
            $table->unsignedInteger('min_people');
            $table->unsignedInteger('max_people');
            $table->time('session_duration');
            $table->time('session_interval');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_policies');
    }
};
