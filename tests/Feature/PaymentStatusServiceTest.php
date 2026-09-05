<?php

namespace Tests\Feature;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Models\BankReceipt;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PaymentStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_pending_review_for_non_receipt_payment(): void
    {
        [, $payment] = $this->createPayment('online', 'gateway', 'pending');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('وضعیت در انتظار بررسی فقط برای پرداخت‌های رسید بانکی مجاز است.');

        app(PaymentStatusService::class)->applyManualStatus($payment, 'pending_review', 1);
    }

    public function test_it_rejects_paid_for_receipt_payment_without_uploaded_receipt(): void
    {
        [, $payment] = $this->createPayment('bank_receipt', 'receipt', 'initiated');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('برای این تغییر وضعیت، ابتدا باید یک رسید بانکی ثبت شده باشد.');

        app(PaymentStatusService::class)->applyManualStatus($payment, 'paid', 1);
    }

    public function test_it_applies_pending_review_for_receipt_payment_with_uploaded_receipt_and_records_history(): void
    {
        [$user, $payment] = $this->createPayment('bank_receipt', 'receipt', 'initiated');

        BankReceipt::create([
            'payment_id' => $payment->id,
            'tracking_number' => 'TRX-2001',
            'note' => 'رسید تست',
            'file_path' => 'receipts/test.pdf',
            'original_name' => 'test.pdf',
            'uploaded_by' => $user->id,
            'uploaded_at' => now(),
            'status' => 'pending_review',
        ]);

        $updatedPayment = app(PaymentStatusService::class)->applyManualStatus(
            $payment,
            'pending_review',
            $user->id
        );

        $this->assertSame('pending_review', $updatedPayment->status);
        $this->assertDatabaseHas('payment_status_histories', [
            'payment_id' => $payment->id,
            'from_status' => 'initiated',
            'to_status' => 'pending_review',
            'changed_by' => $user->id,
        ]);
    }

    private function createPayment(string $methodName, string $methodType, string $paymentStatus): array
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260905-' . strtoupper(substr(uniqid(), -6)),
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);

        $method = PaymentMethod::create([
            'name' => $methodName,
            'type' => $methodType,
            'title' => 'روش پرداخت تست',
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 100000,
            'status' => $paymentStatus,
        ]);

        return [$user, $payment];
    }
}