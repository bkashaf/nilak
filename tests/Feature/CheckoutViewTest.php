<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_sees_address_source_and_receipt_config_hooks(): void
    {
        $user = User::factory()->create([
            'address' => 'تهران، خیابان آزادی، پلاک 10',
            'mobile' => '09120000000',
            'secondary_phone' => '02112345678',
        ]);

        PaymentMethod::create([
            'name' => 'bank_receipt',
            'title' => 'پرداخت با رسید بانکی',
            'type' => 'receipt',
            'is_active' => true,
            'config' => [
                'bank_name' => 'Melli',
                'account_holder' => 'Nilak Store',
                'card_last4' => '1234',
                'iban' => 'IR000000000000000000000000',
            ],
        ]);

        session(['cart' => collect([
            '1' => [
                'product_id' => 1,
                'price' => 10000,
                'quantity' => 1,
            ],
        ])]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertOk();
        $response->assertSee('name="address_source"', false);
        $response->assertSee('receiptInfoBox', false);
        $response->assertSee('data-type="receipt"', false);
        $response->assertSee('data-config=', false);
    }
}
