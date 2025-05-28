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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'preparing', 'prepared',
                'delivering', 'delivered', 'picked_up'])->default('pending');

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_data');

            $table->foreignId('delivery_driver_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->json('delivery_driver_data');

            $table->enum('receiving_method', ['delivery', 'pick_up', 'on_table'])->default('delivery');
            $table->enum('payment_method', ['cash', 'wallet'])->default('cash');

            $table->decimal('price');
            $table->decimal('delivery_price')->default(0);
            $table->decimal('discount_price')->default(0);
            $table->decimal('total_price');

            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
