<?php

namespace Tests\Feature;

use App\Domain\Payment\Services\RefundService;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_process_partial_refund_without_exceeding_paid_amount(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($role);
        $order = Order::create(['user_id' => $admin->id, 'total_amount' => 100000, 'status' => 'paid', 'tracking_code' => 'NLK-REF-001', 'address' => 'تهران، خیابان نمونه، پلاک ۱']);
        $method = PaymentMethod::create(['name' => 'online', 'type' => 'gateway', 'title' => 'پرداخت آنلاین', 'is_active' => true]);
        $payment = Payment::create(['order_id' => $order->id, 'payment_method_id' => $method->id, 'amount' => 100000, 'status' => 'paid', 'gateway_name' => 'fake']);

        $refund = app(RefundService::class)->process($payment, 40000, $admin->id, 'مرجوعی بخشی');

        $this->assertSame('completed', $refund->status);
        $this->assertSame(40000, (int) $refund->amount);
        $this->expectException(\RuntimeException::class);
        app(RefundService::class)->process($payment, 60001, $admin->id);
    }
}
