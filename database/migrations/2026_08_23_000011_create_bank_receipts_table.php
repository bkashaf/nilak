<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('status')->default('pending_review');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        DB::table('payments')->select(['id', 'callback_data'])->orderBy('id')->each(function ($payment) {
            $path = data_get(json_decode($payment->callback_data ?: '{}', true), 'receipt_path');
            if ($path) {
                DB::table('bank_receipts')->insert([
                    'payment_id' => $payment->id,
                    'file_path' => $path,
                    'status' => $payment->status === 'pending_review' ? 'pending_review' : $payment->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_receipts');
    }
};
