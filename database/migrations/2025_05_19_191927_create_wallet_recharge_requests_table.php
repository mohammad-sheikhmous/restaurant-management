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
        Schema::create('wallet_recharge_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_data');

            $table->decimal('amount')->nullable();
            $table->enum('transfer_method', ['bank', 'cash']);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->string('proof_image');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_recharge_requests');
    }
};
