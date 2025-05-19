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
        Schema::create('closed_periods', function (Blueprint $table) {
            $table->id();

            $table->boolean('full_day')->default(1);

            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();

            $table->date('from_date');
            $table->date('to_date');

            $table->string('reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closed_periods');
    }
};
