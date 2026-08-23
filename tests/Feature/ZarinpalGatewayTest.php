<?php

namespace Tests\Feature;

use App\Domain\Payment\Gateways\ZarinpalGateway;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZarinpalGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_initiates_and_verifies_a_zarinpal_payment(): void
    {
        config()->set('payment.gateways.zarinpal.enabled', true);
        config()->set('payment.gateways.zarinpal.merchant_id', 'merchant-test');
        config()->set('payment.gateways.zarinpal.endpoint', 'https://zarinpal.test/payment');
        Http::fake([
            'https://zarinpal.test/payment/request.json' => Http::response([
                'data' => ['code' => 100, 'authority' => 'A000000000000000000000000001'],
            ]),
            'https://zarinpal.test/payment/verify.json' => Http::response([
                'data' => ['code' => 100, 'ref_id' => 987654],
            ]),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260823-ZARIN1',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
        $method = PaymentMethod::create([
            'name' => 'online',
            'type' => 'gateway',
            'title' => 'پرداخت آنلاین',
            'config' => ['gateway' => 'zarinpal'],
            'is_active' => true,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 100000,
            'status' => 'initiated',
            'gateway_name' => 'zarinpal',
        ]);

        $gateway = app(ZarinpalGateway::class);
        $initiate = $gateway->initiate($payment->load('order.user'));
        $verify = $gateway->verify($payment->fresh(), ['Authority' => $initiate['authority']]);

        $this->assertSame('initiated', $initiate['status']);
        $this->assertSame('paid', $verify['status']);
        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('987654', (string) $payment->fresh()->gateway_transaction_id);
        Http::assertSentCount(2);
    }
}
