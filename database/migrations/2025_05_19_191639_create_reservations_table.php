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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->string('revs_number');

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_data');

            $table->date('revs_date');
            $table->time('revs_time');
            $table->time('revs_duration');

            $table->unsignedInteger('guests_count');

            $table->enum('status', [
                'not_confirmed','pending', 'accepted', 'rejected', 'active', 'no_show', 'cancelled', 'completed'
            ])->default('pending');

            $table->unsignedInteger('deposit_value');
            $table->enum('deposit_status', ['pending', 'refunded', 'forfeited']);

            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
