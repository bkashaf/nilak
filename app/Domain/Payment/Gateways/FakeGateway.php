<?php

namespace App\Domain\Payment\Gateways;

use App\Models\Payment;
use App\Domain\Payment\Services\PaymentStatusService;

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
        app(PaymentStatusService::class)->markPaid(
            $payment,
            'FAKE-' . uniqid(),
            $callbackData,
        );

        return [
            'status' => 'paid',
            'payment_id' => $payment->id,
            'message' => 'Fake payment verified successfully.'
        ];
    }
}
