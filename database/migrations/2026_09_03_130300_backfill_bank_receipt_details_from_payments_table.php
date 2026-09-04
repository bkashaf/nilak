<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->select([
                'payments.id',
                'payments.callback_data',
                'payments.updated_at',
                'orders.user_id',
            ])
            ->orderBy('payments.id')
            ->get()
            ->each(function ($payment) {
                $callbackData = json_decode($payment->callback_data ?: '{}', true);

                if (! is_array($callbackData)) {
                    $callbackData = [];
                }

                $filePath = data_get($callbackData, 'receipt_path');
                $trackingNumber = data_get($callbackData, 'tracking_number');
                $note = data_get($callbackData, 'note');
                $uploadedAt = data_get($callbackData, 'uploaded_at');

                if (! $filePath && ! $trackingNumber && ! $note) {
                    return;
                }

                $receipt = DB::table('bank_receipts')
                    ->where('payment_id', $payment->id)
                    ->orderByDesc('id')
                    ->first();

                if (! $receipt) {
                    DB::table('bank_receipts')->insert([
                        'payment_id' => $payment->id,
                        'tracking_number' => $trackingNumber,
                        'note' => $note,
                        'file_path' => $filePath,
                        'original_name' => $filePath ? basename($filePath) : null,
                        'uploaded_by' => $payment->user_id,
                        'uploaded_at' => $uploadedAt ?: $payment->updated_at,
                        'status' => 'pending_review',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    return;
                }

                DB::table('bank_receipts')
                    ->where('id', $receipt->id)
                    ->update([
                        'tracking_number' => $receipt->tracking_number ?: $trackingNumber,
                        'note' => $receipt->note ?: $note,
                        'file_path' => $receipt->file_path ?: $filePath,
                        'original_name' => $receipt->original_name ?: ($filePath ? basename($filePath) : null),
                        'uploaded_by' => $receipt->uploaded_by ?: $payment->user_id,
                        'uploaded_at' => $receipt->uploaded_at ?: ($uploadedAt ?: $payment->updated_at),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
    }
};