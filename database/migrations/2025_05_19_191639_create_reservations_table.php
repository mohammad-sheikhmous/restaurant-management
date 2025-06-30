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

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_data');

            $table->date('res_date');
            $table->time('res_time');
            $table->time('res_duration');

            $table->unsignedInteger('guests_count');

            $table->enum('status', [
                'pending', 'accepted', 'rejected', 'active', 'no_show', 'cancelled', 'completed'
            ])->default('pending');

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
