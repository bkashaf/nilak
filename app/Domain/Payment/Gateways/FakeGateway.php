<?php

namespace App\Domain\Payment\Gateways;

use App\Models\Payment;

class FakeGateway implements GatewayInterface
{
    /**
     * شروع پرداخت (تستی)
     */
    public function initiate(Payment $payment)
    {
        return [
            'status' => 'initiated',
            'payment_id' => $payment->id,
            'redirect_url' => url('/fake-gateway/pay/' . $payment->id),
            'message' => 'Fake payment initiated successfully.'
        ];
    }

    /**
     * تأیید پرداخت (تستی)
     */
    public function verify(Payment $payment, array $callbackData)
    {
        // شبیه‌سازی موفقیت پرداخت
        $payment->update([
            'status' => 'paid',
            'gateway_transaction_id' => 'FAKE-' . uniqid(),
            'callback_data' => $callbackData,
            'paid_at' => now(),
        ]);

        return [
            'status' => 'paid',
            'payment_id' => $payment->id,
            'message' => 'Fake payment verified successfully.'
        ];
    }
}
