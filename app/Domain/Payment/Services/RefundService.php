<?php

namespace App\Domain\Payment\Services;

use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RefundService
{
    public function process(Payment $payment, int $amount, int $adminId, ?string $reason = null): Refund
    {
        return DB::transaction(function () use ($payment, $amount, $adminId, $reason) {
            $payment->refresh();
            if ($payment->status !== 'paid') {
                throw new RuntimeException('فقط پرداخت موفق قابل بازپرداخت است.');
            }

            $refunded = (int) $payment->refunds()->where('status', 'completed')->sum('amount');
            if ($amount < 1 || $refunded + $amount > (int) $payment->amount) {
                throw new RuntimeException('مبلغ بازپرداخت از مبلغ قابل بازپرداخت بیشتر است.');
            }

            return Refund::create([
                'payment_id' => $payment->id,
                'amount' => $amount,
                'status' => 'completed',
                'reason' => $reason,
                'processed_by' => $adminId,
                'processed_at' => now(),
            ]);
        });
    }
}
