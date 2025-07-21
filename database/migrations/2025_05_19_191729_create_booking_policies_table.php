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

            $table->unsignedTinyInteger('max_revs_duration_hours');
            $table->unsignedTinyInteger('max_pre_booking_days');
            $table->unsignedInteger('min_pre_booking_minutes');
            $table->boolean('revs_cancellability');
            $table->unsignedInteger('min_revs_cancellability_minutes')->nullable();
            $table->unsignedInteger('revs_cancellability_ratio')->nullable();
            $table->boolean('revs_modifiability');
            $table->unsignedInteger('min_revs_modifiability_minutes')->nullable();
            $table->unsignedInteger('revs_modifiability_ratio')->nullable();
            $table->boolean('table_combinability');
            $table->boolean('manual_confirmation');
            $table->unsignedSmallInteger('min_people');
            $table->unsignedSmallInteger('max_people');
            $table->unsignedSmallInteger('interval_minutes');
            $table->unsignedSmallInteger('auto_no_show_minutes');

            $table->boolean('deposit_system');
            $table->decimal('deposit_value')->default(0);
            $table->unsignedTinyInteger('num_of_person_per_deposit')->nullable();
            $table->unsignedSmallInteger('time_per_deposit')->nullable();
            $table->boolean('deposit_customizability')->nullable();

            $table->text('explanatory_notes')->nullable();
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
