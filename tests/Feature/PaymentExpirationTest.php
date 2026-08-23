<?php

namespace Tests\Feature;

use App\Domain\Inventory\InventoryReservationService;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_payment_releases_reserved_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'لباس', 'slug' => 'expiration-clothing', 'status' => 1, 'position' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'EXP-001',
            'name' => 'محصول انقضا',
            'slug' => 'expiration-product',
            'price' => 1000,
            'stock' => 5,
            'reserved_stock' => 0,
            'is_active' => true,
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 2000,
            'status' => 'pending',
            'inventory_status' => 'none',
            'tracking_code' => 'NLK-EXP-001',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'price' => 1000, 'total' => 2000]);
        app(InventoryReservationService::class)->reserve($order->fresh()->load('items'));
        $method = PaymentMethod::create(['name' => 'online', 'type' => 'gateway', 'title' => 'پرداخت آنلاین', 'is_active' => true]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => 2000,
            'status' => 'initiated',
            'gateway_name' => 'fake',
            'expires_at' => now()->subMinute(),
        ]);

        $this->artisan('payments:expire')->assertExitCode(0);

        $this->assertSame('expired', $payment->fresh()->status);
        $this->assertSame('released', $order->fresh()->inventory_status);
        $this->assertSame(5, (int) $product->fresh()->stock);
        $this->assertSame(0, (int) $product->fresh()->reserved_stock);
    }
}
