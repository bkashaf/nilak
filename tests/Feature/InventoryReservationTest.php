<?php

namespace Tests\Feature;

use App\Domain\Inventory\InventoryReservationService;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reserved_stock_is_committed_once(): void
    {
        [$order, $product] = $this->orderWithItem();
        $service = app(InventoryReservationService::class);

        $service->reserve($order->load('items'));
        $service->commit($order->fresh()->load('items'));
        $service->commit($order->fresh()->load('items'));

        $product = $product->fresh();
        $this->assertSame(3, (int) $product->stock);
        $this->assertSame(0, (int) $product->reserved_stock);
        $this->assertSame('committed', $order->fresh()->inventory_status);
    }

    public function test_reserved_stock_is_released_once(): void
    {
        [$order, $product] = $this->orderWithItem();
        $service = app(InventoryReservationService::class);

        $service->reserve($order->load('items'));
        $service->release($order->fresh()->load('items'));
        $service->release($order->fresh()->load('items'));

        $product = $product->fresh();
        $this->assertSame(5, (int) $product->stock);
        $this->assertSame(0, (int) $product->reserved_stock);
        $this->assertSame('released', $order->fresh()->inventory_status);
    }

    private function orderWithItem(): array
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'لباس', 'slug' => uniqid('inventory-'), 'status' => 1, 'position' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => uniqid('INV-'),
            'name' => 'محصول موجودی',
            'slug' => uniqid('inventory-product-'),
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
            'tracking_code' => uniqid('NLK-'),
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 1000,
            'total' => 2000,
        ]);

        return [$order, $product];
    }
}
