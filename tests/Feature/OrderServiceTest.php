<?php

namespace Tests\Feature;

use App\Domain\Cart\CartService;
use App\Domain\Order\OrderService;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Role;
use App\Models\CategoryTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_cod_order_and_reserves_stock(): void
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
        $this->assertSame(5, (int) $product->fresh()->stock);
        $this->assertSame(2, (int) $product->fresh()->reserved_stock);
        $this->assertSame('reserved', $result['order']->fresh()->inventory_status);
        $this->assertTrue($cart->all()->isEmpty());
        $this->assertDatabaseHas('orders', [
            'id' => $result['order']->id,
            'user_id' => $user->id,
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
    }

    public function test_customer_can_track_an_order_by_tracking_code(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 99000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260823-ABC123',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);

        $response = $this->post(route('orders.track'), [
            'tracking_code' => 'nlk-20260823-abc123',
        ]);

        $response->assertOk();
        $response->assertSee('NLK-20260823-ABC123');
        $response->assertDontSee('Undefined array key');
    }

    public function test_admin_can_update_order_and_payment_status_separately(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($adminRole);
        $order = Order::create([
            'user_id' => $admin->id,
            'total_amount' => 99000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260823-ADMIN01',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);
        $paymentMethod = PaymentMethod::create([
            'name' => 'cod',
            'type' => 'cod',
            'title' => 'پرداخت در محل',
            'is_active' => true,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => 99000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => 'shipped',
            'payment_status' => 'paid',
            'tracking_code' => $order->tracking_code,
        ]);

        $response->assertRedirect(route('admin.orders.edit', $order));
        $this->assertSame('shipped', $order->fresh()->status);
        $this->assertSame('paid', $payment->fresh()->status);
    }

    public function test_product_and_category_use_the_current_locale_translation(): void
    {
        $category = Category::create([
            'name' => 'لباس',
            'slug' => 'clothing',
            'status' => 1,
            'position' => 1,
        ]);
        CategoryTranslation::create([
            'category_id' => $category->id,
            'locale' => 'en',
            'name' => 'Clothing',
            'is_published' => true,
        ]);

        app()->setLocale('en');

        $this->assertSame('Clothing', $category->fresh()->localized_name);
        $this->assertSame('clothing', $category->fresh()->slug);
    }
}
