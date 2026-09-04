<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_discount_and_price_history(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('discount-category');

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name_fa' => 'کفش رانینگ',
            'slug' => 'running-shoe',
            'price' => 199000,
            'compare_price' => 255000,
            'stock' => 5,
            'category_id' => $category->id,
            'is_active' => 'on',
            'is_featured' => 'on',
            'price_change_reason' => 'تعریف قیمت اولیه با تخفیف',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product = Product::where('slug', 'running-shoe')->firstOrFail();

        $this->assertSame('کفش رانینگ', $product->name);
        $this->assertSame('199000.00', $product->price);
        $this->assertSame('255000.00', $product->compare_price);
        $this->assertTrue($product->is_active);
        $this->assertTrue($product->is_featured);
        $this->assertTrue($product->has_discount);
        $this->assertSame(22, $product->discount_percent);

        $this->assertDatabaseHas('product_price_histories', [
            'product_id' => $product->id,
            'changed_by' => $admin->id,
            'old_price' => null,
            'new_price' => '199000.00',
            'old_compare_price' => null,
            'new_compare_price' => '255000.00',
            'change_type' => 'initial',
            'reason' => 'تعریف قیمت اولیه با تخفیف',
        ]);
    }

    public function test_admin_can_create_product_with_unchecked_booleans(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('unchecked-category');

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name_fa' => 'محصول غیرفعال',
            'slug' => 'inactive-product',
            'price' => 120000,
            'compare_price' => null,
            'stock' => 2,
            'category_id' => $category->id,
            'price_change_reason' => 'ایجاد بدون ویژگی ویژه',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product = Product::where('slug', 'inactive-product')->firstOrFail();

        $this->assertFalse($product->is_active);
        $this->assertFalse($product->is_featured);
        $this->assertFalse($product->has_discount);
        $this->assertNull($product->compare_price);

        $this->assertDatabaseHas('product_price_histories', [
            'product_id' => $product->id,
            'changed_by' => $admin->id,
            'old_price' => null,
            'new_price' => '120000.00',
            'old_compare_price' => null,
            'new_compare_price' => null,
            'change_type' => 'initial',
            'reason' => 'ایجاد بدون ویژگی ویژه',
        ]);
    }

    public function test_admin_cannot_create_product_with_compare_price_lower_than_price(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('invalid-category');

        $response = $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), [
                'name_fa' => 'کفش نامعتبر',
                'slug' => 'invalid-shoe',
                'price' => 255000,
                'compare_price' => 199000,
                'stock' => 3,
                'category_id' => $category->id,
                'is_active' => 'on',
            ]);

        $response->assertRedirect(route('admin.products.create'));
        $response->assertSessionHasErrors(['compare_price']);

        $this->assertDatabaseMissing('products', [
            'slug' => 'invalid-shoe',
        ]);

        $this->assertDatabaseCount('product_price_histories', 0);
    }

    public function test_admin_can_update_product_price_and_a_history_row_is_created(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('update-category');

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRICE-001',
            'name' => 'کفش تمرینی',
            'slug' => 'training-shoe',
            'price' => 255000,
            'compare_price' => 300000,
            'stock' => 4,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name_fa' => 'کفش تمرینی',
            'slug' => 'training-shoe',
            'price' => 199000,
            'compare_price' => 255000,
            'stock' => 7,
            'category_id' => $category->id,
            'is_active' => 'on',
            'price_change_reason' => 'اعمال تخفیف مناسبتی',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertSame('199000.00', $product->price);
        $this->assertSame('255000.00', $product->compare_price);
        $this->assertTrue($product->is_active);
        $this->assertFalse($product->is_featured);
        $this->assertSame(22, $product->discount_percent);

        $this->assertDatabaseHas('product_price_histories', [
            'product_id' => $product->id,
            'changed_by' => $admin->id,
            'old_price' => '255000.00',
            'new_price' => '199000.00',
            'old_compare_price' => '300000.00',
            'new_compare_price' => '255000.00',
            'change_type' => 'manual',
            'reason' => 'اعمال تخفیف مناسبتی',
        ]);
    }

    public function test_compare_price_is_cleared_when_equal_to_current_price(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('equal-price-category');

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRICE-002',
            'name' => 'محصول بدون تخفیف',
            'slug' => 'plain-product',
            'price' => 200000,
            'compare_price' => 240000,
            'stock' => 8,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name_fa' => 'محصول بدون تخفیف',
            'slug' => 'plain-product',
            'price' => 200000,
            'compare_price' => 200000,
            'stock' => 8,
            'category_id' => $category->id,
            'is_active' => 'on',
            'price_change_reason' => 'حذف تخفیف',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertSame('200000.00', $product->price);
        $this->assertNull($product->compare_price);
        $this->assertFalse($product->has_discount);
        $this->assertNull($product->discount_percent);

        $this->assertDatabaseHas('product_price_histories', [
            'product_id' => $product->id,
            'changed_by' => $admin->id,
            'old_price' => '200000.00',
            'new_price' => '200000.00',
            'old_compare_price' => '240000.00',
            'new_compare_price' => null,
            'change_type' => 'manual',
            'reason' => 'حذف تخفیف',
        ]);
    }

    public function test_compare_price_is_cleared_when_sent_as_empty_string(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('empty-compare-category');

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRICE-003',
            'name' => 'محصول با تخفیف موقت',
            'slug' => 'temporary-discount-product',
            'price' => 180000,
            'compare_price' => 220000,
            'stock' => 6,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name_fa' => 'محصول با تخفیف موقت',
            'slug' => 'temporary-discount-product',
            'price' => 180000,
            'compare_price' => '',
            'stock' => 6,
            'category_id' => $category->id,
            'is_active' => 'on',
            'price_change_reason' => 'حذف قیمت مرجع',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertNull($product->compare_price);
        $this->assertFalse($product->has_discount);

        $this->assertDatabaseHas('product_price_histories', [
            'product_id' => $product->id,
            'changed_by' => $admin->id,
            'old_price' => '180000.00',
            'new_price' => '180000.00',
            'old_compare_price' => '220000.00',
            'new_compare_price' => null,
            'change_type' => 'manual',
            'reason' => 'حذف قیمت مرجع',
        ]);
    }

    public function test_no_price_history_row_is_created_when_price_fields_do_not_change(): void
    {
        $admin = $this->adminUser();
        $category = $this->createCategory('no-change-category');

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRICE-004',
            'name' => 'محصول ثابت',
            'slug' => 'stable-product',
            'price' => 175000,
            'compare_price' => 210000,
            'stock' => 3,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name_fa' => 'محصول ثابت و ویرایش‌شده',
            'slug' => 'stable-product',
            'price' => 175000,
            'compare_price' => 210000,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => 'on',
            'price_change_reason' => 'ویرایش غیرقیمتی',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertSame('175000.00', $product->price);
        $this->assertSame('210000.00', $product->compare_price);
        $this->assertSame(10, $product->stock);
        $this->assertSame('محصول ثابت و ویرایش‌شده', $product->name);

        $this->assertDatabaseCount('product_price_histories', 0);
    }

    private function adminUser(): User
    {
        $admin = User::factory()->create();

        $role = Role::firstOrCreate(
            ['name' => 'admin'],
            ['label' => 'مدیر']
        );

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }

    private function createCategory(string $slug = 'test-category'): Category
    {
        return Category::create([
            'name' => 'دسته تست',
            'slug' => $slug,
            'status' => 1,
            'position' => 1,
        ]);
    }
}