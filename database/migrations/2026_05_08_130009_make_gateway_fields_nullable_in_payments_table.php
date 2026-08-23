<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_name')->nullable()->change();
            $table->string('gateway_transaction_id')->nullable()->change();
            $table->json('callback_data')->nullable()->change();
            $table->timestamp('paid_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_name')->nullable(false)->change();
            $table->string('gateway_transaction_id')->nullable(false)->change();
            $table->json('callback_data')->nullable(false)->change();
            $table->timestamp('paid_at')->nullable(false)->change();
        });
    }
};
