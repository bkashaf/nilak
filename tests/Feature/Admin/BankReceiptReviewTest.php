<?php

namespace Tests\Feature\Admin;

use App\Models\BankReceipt;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankReceiptReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_bank_receipt_review_page(): void
    {
        [$admin, $receipt] = $this->createScenario();

        $response = $this->actingAs($admin)->get(route('admin.bank-receipts.show', $receipt));

        $response->assertOk();
        $response->assertSee('شماره پیگیری بانکی', false);
        $response->assertSee('TRX-12345', false);
    }

    public function test_admin_can_reject_bank_receipt(): void
    {
        [$admin, $receipt] = $this->createScenario();

        $response = $this->actingAs($admin)->post(route('admin.bank-receipts.reject', $receipt), [
            'rejection_reason' => 'شماره پیگیری معتبر نیست',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bank_receipts', [
            'id' => $receipt->id,
            'status' => 'rejected',
            'rejection_reason' => 'شماره پیگیری معتبر نیست',
        ]);
    }

    private function createScenario(): array
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($role);

        $customer = User::factory()->create();

        $order = Order::create([
            'user_id' => $customer->id,
            'total_amount' => 200000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260903-ABC123',
            'address' => 'تهران',
        ]);

        $method = PaymentMethod::create([
            'name' => 'bank_receipt',
            'type' => 'receipt',
            'title' => 'رسید بانکی',
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 200000,
            'status' => 'pending_review',
        ]);

        $receipt = BankReceipt::create([
            'payment_id' => $payment->id,
            'tracking_number' => 'TRX-12345',
            'note' => 'واریز شد',
            'status' => 'pending_review',
            'uploaded_by' => $customer->id,
            'uploaded_at' => now(),
        ]);

        return [$admin, $receipt];
    }
}