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

            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->json('table_data');

            $table->date('reservation_date');
            $table->time('reservation_time');

            $table->unsignedInteger('guests_count');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled','finished'])->default('pending');

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
