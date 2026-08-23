<?php

namespace App\Domain\Payment\Services;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Domain\Payment\Gateways\GatewayInterface;
use Illuminate\Support\Facades\App;

class PaymentService
{
    /**
     * شروع فرآیند پرداخت
     */
    public function initiate(Payment $payment)
    {
        if ($payment->status !== 'initiated') {
            throw new \LogicException('این پرداخت در وضعیت شروع پرداخت نیست.');
        }

        if (! $payment->gateway_name) {
            throw new \LogicException('درگاه پرداخت برای این پرداخت تنظیم نشده است.');
        }

        $gateway = $this->resolveGateway($payment->gateway_name);

        return $gateway->initiate($payment);
    }

    /**
     * تأیید پرداخت (callback بانک)
     */
    public function verify(Payment $payment, array $callbackData)
    {
        if ($payment->status === 'paid') {
            return [
                'status' => 'paid',
                'payment_id' => $payment->id,
                'message' => 'این پرداخت قبلاً تأیید شده است.',
            ];
        }

        if ($payment->status !== 'initiated') {
            throw new \LogicException('این پرداخت قابل تأیید نیست.');
        }

        $gateway = $this->resolveGateway($payment->gateway_name);

        return $gateway->verify($payment, $callbackData);
    }

    /**
     * انتخاب درگاه مناسب بر اساس gateway_name
     */
    protected function resolveGateway(string $gatewayName): GatewayInterface
    {
        $class = "App\\Domain\\Payment\\Gateways\\" . ucfirst($gatewayName) . "Gateway";

        if (!class_exists($class)) {
            throw new \Exception("Gateway class not found: {$class}");
        }

        return App::make($class);
    }
}
