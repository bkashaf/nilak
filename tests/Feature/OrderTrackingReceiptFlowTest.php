<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingReceiptFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_receipt_upload_cta_on_order_tracking_page(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'کفش',
            'slug' => 'tracking-shoes',
            'status' => 1,
            'position' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'TRACK-PRD-01',
            'name' => 'کفش پیگیری',
            'slug' => 'tracking-shoe',
            'price' => 250000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 250000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260903-TRACK01',
            'address' => 'تهران',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 250000,
            'total' => 250000,
        ]);

        $method = PaymentMethod::create([
            'name' => 'bank_receipt',
            'type' => 'receipt',
            'title' => 'رسید بانکی',
            'is_active' => true,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 250000,
            'status' => 'initiated',
        ]);

        $response = $this->actingAs($user)->post(route('order.tracking.submit'), [
            'tracking_code' => $order->tracking_code,
        ]);

        $response->assertOk();
        $response->assertSee('مشاهده سفارش و ارسال رسید');
        $response->assertSee(route('account.orders.show', $order), false);
    }
}