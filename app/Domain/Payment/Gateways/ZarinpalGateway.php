<?php

namespace App\Domain\Payment\Gateways;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ZarinpalGateway implements GatewayInterface
{
    public function initiate(Payment $payment): array
    {
        $merchantId = config('payment.gateways.zarinpal.merchant_id');
        $endpoint = rtrim(config('payment.gateways.zarinpal.endpoint'), '/');

        if (! $merchantId) {
            throw new RuntimeException('شناسه پذیرنده زرین‌پال تنظیم نشده است.');
        }

        $response = Http::asJson()->post($endpoint . '/request.json', [
            'merchant_id' => $merchantId,
            'amount' => $this->amount($payment),
            'description' => 'Nilak order ' . $payment->order_id,
            'callback_url' => route('payment.zarinpal.callback', $payment),
            'metadata' => [
                'email' => $payment->order?->user?->email,
            ],
        ])->throw()->json();

        $data = data_get($response, 'data', []);
        if ((int) data_get($data, 'code') !== 100) {
            throw new RuntimeException('ایجاد درخواست پرداخت زرین‌پال ناموفق بود.');
        }

        $payment->update([
            'gateway_transaction_id' => data_get($data, 'authority'),
            'callback_data' => $response,
        ]);

        return [
            'status' => 'initiated',
            'payment_id' => $payment->id,
            'authority' => data_get($data, 'authority'),
            'redirect_url' => 'https://www.zarinpal.com/pg/StartPay/' . data_get($data, 'authority'),
        ];
    }

    public function verify(Payment $payment, array $callbackData): array
    {
        $merchantId = config('payment.gateways.zarinpal.merchant_id');
        $endpoint = rtrim(config('payment.gateways.zarinpal.endpoint'), '/');
        $authority = $callbackData['Authority'] ?? $payment->gateway_transaction_id;

        $response = Http::asJson()->post($endpoint . '/verify.json', [
            'merchant_id' => $merchantId,
            'amount' => $this->amount($payment),
            'authority' => $authority,
        ])->throw()->json();

        $code = (int) data_get($response, 'data.code');
        if (! in_array($code, [100, 101], true)) {
            app(PaymentStatusService::class)->markFailed($payment, 'failed', $response);

            return [
                'status' => 'failed',
                'payment_id' => $payment->id,
                'message' => 'تأیید پرداخت زرین‌پال ناموفق بود.',
            ];
        }

        $transactionId = (string) (data_get($response, 'data.ref_id') ?: $authority);
        app(PaymentStatusService::class)->markPaid($payment, $transactionId, $response);

        return [
            'status' => 'paid',
            'payment_id' => $payment->id,
            'transaction_id' => $transactionId,
        ];
    }

    private function amount(Payment $payment): int
    {
        return (int) $payment->amount * (int) config('payment.gateways.zarinpal.amount_multiplier', 1);
    }
}
