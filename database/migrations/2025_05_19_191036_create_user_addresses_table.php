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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 9, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('label', 30)->nullable();
            $table->string('name', 30);
            $table->string('city', 20);
            $table->string('area', 30);
            $table->string('street', 40);
            $table->string('mobile', 20)->nullable();
            $table->string('additional_details', 150)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
