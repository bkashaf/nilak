<?php

namespace Tests\Feature;

use App\Domain\Cart\CartService;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_checkout_redirects_to_tracking_page(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        PaymentMethod::create([
            'name' => 'cod',
            'type' => 'cod',
            'title' => 'پرداخت در محل',
            'is_active' => true,
        ]);

        app(CartService::class)->add($product->id, 1);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'address_source' => 'new',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
            'payment_method' => 'cod',
            'recipient_name' => 'مشتری نمونه',
            'recipient_mobile' => '09120000000',
            'recipient_phone_alt' => '02112345678',
            'postal_code' => '1234567890',
        ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('/order-tracking?tracking_code=', (string) $response->headers->get('Location'));
    }

    public function test_receipt_checkout_redirects_to_tracking_page(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        PaymentMethod::create([
            'name' => 'bank_receipt',
            'type' => 'receipt',
            'title' => 'رسید بانکی',
            'is_active' => true,
        ]);

        app(CartService::class)->add($product->id, 1);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'address_source' => 'new',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
            'payment_method' => 'bank_receipt',
            'recipient_name' => 'مشتری نمونه',
            'recipient_mobile' => '09120000000',
            'recipient_phone_alt' => '02112345678',
            'postal_code' => '1234567890',
        ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('/order-tracking?tracking_code=', (string) $response->headers->get('Location'));
    }

    public function test_gateway_checkout_redirects_to_gateway_url(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        PaymentMethod::create([
            'name' => 'online',
            'type' => 'gateway',
            'title' => 'پرداخت آنلاین',
            'config' => ['gateway' => 'fake'],
            'is_active' => true,
        ]);

        app(CartService::class)->add($product->id, 1);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'address_source' => 'new',
            'address' => 'تهران، خیابان نمونه، پلاک ۱',
            'payment_method' => 'online',
            'recipient_name' => 'مشتری نمونه',
            'recipient_mobile' => '09120000000',
            'recipient_phone_alt' => '02112345678',
            'postal_code' => '1234567890',
        ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('/fake-gateway/pay/', (string) $response->headers->get('Location'));
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'دسته تست',
            'slug' => 'test-category',
            'status' => 1,
            'position' => 1,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'sku' => 'CHK-001',
            'name' => 'محصول تستی',
            'slug' => 'checkout-test-product',
            'price' => 100000,
            'stock' => 5,
            'is_active' => true,
        ]);
    }
}
