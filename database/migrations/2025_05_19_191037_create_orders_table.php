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

            $table->string('order_number', 20)->unique()->nullable();

            $table->enum('status', ['pending', 'cancelled', 'accepted', 'rejected', 'preparing', 'prepared',
                'delivering', 'delivered', 'picked_up'])->default('pending');

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_address_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_data');

            $table->foreignId('delivery_driver_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->json('delivery_driver_data')->nullable();

            $table->enum('receiving_method', ['delivery', 'pick_up', 'on_table'])->default('delivery');
            $table->enum('payment_method', ['cash', 'wallet'])->default('cash');

            $table->decimal('total_price', 8, 0);
            $table->decimal('delivery_fee', 8, 0)->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('final_price', 8, 0)->storedAs("total_price + delivery_fee - discount");

            $table->time('estimated_receiving_time')->nullable();
            $table->time('receiving_time')->nullable();

            $table->json('notes')->nullable();

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
