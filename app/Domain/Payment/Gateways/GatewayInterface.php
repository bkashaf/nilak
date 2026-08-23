<?php

namespace App\Domain\Payment\Gateways;

use App\Models\Payment;

interface GatewayInterface
{
    /**
     * شروع فرآیند پرداخت
     * خروجی معمولاً شامل لینک پرداخت یا توکن است
     */
    public function initiate(Payment $payment);

    /**
     * تأیید پرداخت (callback بانک)
     * خروجی شامل وضعیت نهایی پرداخت است
     */
    public function verify(Payment $payment, array $callbackData);
}
