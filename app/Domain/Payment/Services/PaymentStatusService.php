<?php

namespace App\Domain\Payment\Services;

use App\Models\Payment;
use App\Models\PaymentStatusHistory;
use Illuminate\Support\Facades\DB;

class PaymentStatusService
{
    public function markPaid(Payment $payment, ?string $transactionId = null, ?array $callbackData = null, ?int $changedBy = null): Payment
    {
        return DB::transaction(function () use ($payment, $transactionId, $callbackData, $changedBy) {
            $oldStatus = $payment->status;
            $payment->update([
                'status' => 'paid',
                'gateway_transaction_id' => $transactionId ?? $payment->gateway_transaction_id,
                'callback_data' => $callbackData ?? $payment->callback_data,
                'paid_at' => $payment->paid_at ?? now(),
            ]);
            $this->record($payment, $oldStatus, 'paid', $changedBy);

            $payment->order()->where('status', 'pending')->update(['status' => 'paid']);

            return $payment->fresh(['order', 'method']);
        });
    }

    public function markFailed(Payment $payment, string $status = 'failed', ?array $callbackData = null, ?int $changedBy = null): Payment
    {
        $oldStatus = $payment->status;
        $payment->update([
            'status' => $status,
            'callback_data' => $callbackData ?? $payment->callback_data,
            'paid_at' => null,
        ]);
        $this->record($payment, $oldStatus, $status, $changedBy);

        return $payment->fresh(['order', 'method']);
    }

    private function record(Payment $payment, ?string $from, string $to, ?int $changedBy): void
    {
        if ($from === $to) {
            return;
        }

        PaymentStatusHistory::create([
            'payment_id' => $payment->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by' => $changedBy,
        ]);
    }
}
