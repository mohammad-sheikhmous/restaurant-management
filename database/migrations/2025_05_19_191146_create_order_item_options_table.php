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
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_attribute_option_id')->nullable()->constrained()->nullOnDelete();
            $table->string('option_attribute_name');
            $table->string('option_attribute_type');
            $table->string('option_name');
            $table->decimal('option_price', 8, 0);

            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};
