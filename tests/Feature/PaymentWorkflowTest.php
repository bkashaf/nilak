<?php

namespace Tests\Feature;

use App\Models\BankReceipt;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_receipt_for_receipt_payment(): void
    {
        Storage::fake('public');
        [$user, $payment] = $this->createPayment('receipt', 'initiated');
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/payment/upload-receipt', [
            'payment_id' => $payment->id,
            'receipt' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
        ]);

        $response->assertOk();
        $this->assertSame('pending_review', $payment->fresh()->status);
        $this->assertNotNull(data_get($payment->fresh()->callback_data, 'receipt_path'));
    }

    public function test_admin_can_approve_pending_receipt_and_order_becomes_paid(): void
    {
        [$user, $payment] = $this->createPayment('receipt', 'pending_review');

        BankReceipt::create([
            'payment_id' => $payment->id,
            'tracking_number' => 'TRK-1001',
            'note' => 'رسید تست',
            'file_path' => 'receipts/test-receipt.pdf',
            'original_name' => 'test-receipt.pdf',
            'uploaded_by' => $user->id,
            'uploaded_at' => now(),
            'status' => 'pending_review',
        ]);

        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($adminRole);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/payment/approve-receipt', [
            'payment_id' => $payment->id,
            'approved' => true,
        ]);

        $response->assertOk();
        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame('paid', $payment->order->fresh()->status);
        $this->assertDatabaseHas('payment_status_histories', [
            'payment_id' => $payment->id,
            'from_status' => 'pending_review',
            'to_status' => 'paid',
            'changed_by' => $admin->id,
        ]);
    }

    public function test_admin_cannot_mark_non_receipt_payment_as_pending_review(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($adminRole);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260823-GATEWAY1',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);

        $method = PaymentMethod::create([
            'name' => 'online',
            'type' => 'gateway',
            'title' => 'پرداخت آنلاین',
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 100000,
            'status' => 'pending',
        ]);

        $response = $this->from(route('admin.payments.index'))
            ->actingAs($admin)
            ->put(route('admin.payments.update', $payment), [
                'status' => 'pending_review',
            ]);

        $response->assertRedirect(route('admin.payments.index'));
        $response->assertSessionHas('error', 'وضعیت در انتظار بررسی فقط برای پرداخت‌های رسید بانکی مجاز است.');

        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertSame($user->id, $payment->order->fresh()->user_id);
    }

    private function createPayment(string $type, string $status): array
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260823-' . strtoupper(substr(uniqid(), -6)),
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
        $method = PaymentMethod::create([
            'name' => 'bank_receipt',
            'type' => $type,
            'title' => 'رسید بانکی',
            'is_active' => true,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 100000,
            'status' => $status,
        ]);

        return [$user, $payment];
    }
}