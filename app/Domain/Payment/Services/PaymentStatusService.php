<?php

namespace App\Domain\Payment\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentStatusService
{
    public function markPaid(Payment $payment, ?string $transactionId = null, ?array $callbackData = null): Payment
    {
        return DB::transaction(function () use ($payment, $transactionId, $callbackData) {
            $payment->update([
                'status' => 'paid',
                'gateway_transaction_id' => $transactionId ?? $payment->gateway_transaction_id,
                'callback_data' => $callbackData ?? $payment->callback_data,
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            $payment->order()->where('status', 'pending')->update(['status' => 'paid']);

            return $payment->fresh(['order', 'method']);
        });
    }

    public function markFailed(Payment $payment, string $status = 'failed', ?array $callbackData = null): Payment
    {
        $payment->update([
            'status' => $status,
            'callback_data' => $callbackData ?? $payment->callback_data,
            'paid_at' => null,
        ]);

        return $payment->fresh(['order', 'method']);
    }
}
