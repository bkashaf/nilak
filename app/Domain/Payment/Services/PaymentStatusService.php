<?php

namespace App\Domain\Payment\Services;

use App\Models\Payment;
use App\Models\PaymentStatusHistory;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

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

            app(\App\Domain\Inventory\InventoryReservationService::class)->commit($payment->order->load('items'));
            $this->record($payment, $oldStatus, 'paid', $changedBy);

            $payment->order()->where('status', 'pending')->update(['status' => 'paid']);

            return $payment->fresh(['order', 'method']);
        });
    }

    public function markFailed(Payment $payment, string $status = 'failed', ?array $callbackData = null, ?int $changedBy = null): Payment
    {
        if (! in_array($status, ['failed', 'rejected', 'expired'], true)) {
            throw new InvalidArgumentException('Unsupported failed payment status.');
        }

        return DB::transaction(function () use ($payment, $status, $callbackData, $changedBy) {
            $oldStatus = $payment->status;

            $payment->update([
                'status' => $status,
                'callback_data' => $callbackData ?? $payment->callback_data,
                'paid_at' => null,
            ]);

            app(\App\Domain\Inventory\InventoryReservationService::class)->release($payment->order->load('items'));
            $this->record($payment, $oldStatus, $status, $changedBy);

            return $payment->fresh(['order', 'method']);
        });
    }

    public function applyManualStatus(
        Payment $payment,
        string $targetStatus,
        ?int $changedBy = null,
        ?array $callbackData = null
    ): Payment {
        $payment->loadMissing([
            'order',
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ]);

        $this->assertManualTransitionAllowed($payment, $targetStatus);

        if ($targetStatus === 'paid') {
            return $this->markPaid($payment, null, $callbackData, $changedBy);
        }

        if (in_array($targetStatus, ['failed', 'rejected', 'expired'], true)) {
            return $this->markFailed($payment, $targetStatus, $callbackData, $changedBy);
        }

        return DB::transaction(function () use ($payment, $targetStatus, $changedBy, $callbackData) {
            $oldStatus = $payment->status;

            $payment->update([
                'status' => $targetStatus,
                'callback_data' => $callbackData ?? $payment->callback_data,
                'paid_at' => null,
            ]);

            $this->record($payment, $oldStatus, $targetStatus, $changedBy);

            return $payment->fresh(['order', 'method']);
        });
    }

    public function canApplyManualStatus(Payment $payment, string $targetStatus): bool
    {
        try {
            $this->assertManualTransitionAllowed($payment, $targetStatus);

            return true;
        } catch (RuntimeException | InvalidArgumentException) {
            return false;
        }
    }

    public function assertManualTransitionAllowed(Payment $payment, string $targetStatus): void
    {
        if (! in_array($targetStatus, ['pending', 'initiated', 'pending_review', 'paid', 'failed', 'rejected', 'expired'], true)) {
            throw new InvalidArgumentException('Unsupported payment status.');
        }

        if ($targetStatus === 'pending_review' && ! $payment->isReceiptPayment()) {
            throw new RuntimeException('وضعیت در انتظار بررسی فقط برای پرداخت‌های رسید بانکی مجاز است.');
        }

        if (
            in_array($targetStatus, ['paid', 'pending_review'], true)
            && $payment->isReceiptPayment()
            && ! $payment->hasUploadedReceipt()
        ) {
            throw new RuntimeException('برای این تغییر وضعیت، ابتدا باید یک رسید بانکی ثبت شده باشد.');
        }
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