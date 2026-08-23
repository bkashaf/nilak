<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');

            $table->unsignedBigInteger('amount');
            $table->string('status')->default('pending'); // pending, initiated, paid, failed

            $table->string('gateway_name'); // fake, zarinpal, idpay, ...
            $table->string('gateway_transaction_id')->nullable();

            $table->json('callback_data')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
