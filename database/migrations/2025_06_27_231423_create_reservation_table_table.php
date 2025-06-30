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
        Schema::create('reservation_table', function (Blueprint $table) {
            $table->id();

            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->json('table_data');

            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_table');
    }
};
