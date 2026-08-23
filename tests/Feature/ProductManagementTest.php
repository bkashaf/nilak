<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_product_edit_form_with_array_meta(): void
    {
        $admin = $this->adminUser();
        $category = Category::create(['name' => 'لباس', 'slug' => 'clothing', 'status' => 1, 'position' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'EDIT-001',
            'name' => 'محصول ویرایش',
            'slug' => 'edit-product',
            'price' => 1000,
            'stock' => 2,
            'is_active' => true,
            'meta' => ['brand' => 'Nilak'],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertSee('brand');
        $response->assertSee('Nilak');
    }

    public function test_customer_can_add_an_active_product_to_cart(): void
    {
        $category = Category::create(['name' => 'کفش', 'slug' => 'shoes', 'status' => 1, 'position' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'CART-001',
            'name' => 'محصول سبد',
            'slug' => 'cart-product',
            'price' => 2000,
            'stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->post(route('cart.add'), ['product_id' => $product->id, 'qty' => 2]);

        $response->assertRedirect();
        $this->assertSame(2, session('cart.' . $product->id . '.quantity'));
    }

    private function adminUser(): User
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($role);

        return $admin;
    }
}
