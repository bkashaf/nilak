<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_orders_api(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
    }

    public function test_user_can_only_view_their_own_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'total_amount' => 1000,
            'status' => 'pending',
            'tracking_code' => 'NLK-20260823-OWNER1',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson('/api/orders/' . $order->id)->assertNotFound();
    }

    public function test_api_order_uses_database_price_instead_of_client_price(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'لباس',
            'slug' => 'api-clothing',
            'status' => 1,
            'position' => 1,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'API-001',
            'name' => 'محصول API',
            'slug' => 'api-product',
            'price' => 120000,
            'stock' => 5,
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'name' => 'cod',
            'type' => 'cod',
            'title' => 'پرداخت در محل',
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => 1,
            ]],
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
            'payment_method' => 'cod',
        ]);

        $response->assertCreated()
            ->assertJsonPath('order.total_amount', 240000)
            ->assertJsonPath('payment.amount', 240000);
    }
}
