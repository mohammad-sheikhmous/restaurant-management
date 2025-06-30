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
        Schema::create('working_shifts', function (Blueprint $table) {
            $table->id();

            $table->enum('day_of_week', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday',
                'thursday', 'friday']);

            $table->foreignId('type_id')->constrained('reservation_types')->cascadeOnDelete();

            $table->time('opening_time');
            $table->time('closing_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
