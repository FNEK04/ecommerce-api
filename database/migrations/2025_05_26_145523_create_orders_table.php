<?php
// database/migrations/xxxx_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained();
            $table->string('order_number')->unique();
            $table->enum('status', ['pending_payment', 'paid', 'cancelled'])->default('pending_payment');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_url')->unique();
            $table->timestamp('expires_at'); // для отслеживания истечения срока
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};