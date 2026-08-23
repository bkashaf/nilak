<?php

namespace Tests\Feature;

use App\Domain\Cart\CartService;
use App\Domain\Order\OrderService;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_cod_order_from_cart_and_decrements_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'لباس',
            'slug' => 'lebas',
            'status' => 1,
            'position' => 1,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'TEST-001',
            'name' => 'محصول آزمایشی',
            'slug' => 'test-product',
            'price' => 125000,
            'stock' => 5,
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'name' => 'cod',
            'type' => 'cod',
            'title' => 'پرداخت در محل',
            'is_active' => true,
        ]);

        $cart = app(CartService::class);
        $cart->add($product->id, 2);

        $result = app(OrderService::class)->createFromCart(
            $user,
            $cart,
            'تهران، خیابان نمونه، پلاک ۱',
            'cod',
        );

        $this->assertSame(250000, (int) $result['order']->total_amount);
        $this->assertMatchesRegularExpression('/^NLK-\d{8}-[A-Z0-9]{6}$/', $result['order']->tracking_code);
        $this->assertSame(250000, (int) $result['order']->items->first()->total);
        $this->assertSame('pending', $result['payment']->status);
        $this->assertSame(3, (int) $product->fresh()->stock);
        $this->assertTrue($cart->all()->isEmpty());
        $this->assertDatabaseHas('orders', [
            'id' => $result['order']->id,
            'user_id' => $user->id,
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
    }
}
