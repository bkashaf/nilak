<?php

namespace Tests\Feature\Admin;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_multiple_values_for_one_attribute(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'label' => 'مدیر']);
        $admin->roles()->attach($role);

        $category = Category::create([
            'name' => 'لباس',
            'slug' => 'clothing',
            'status' => 1,
            'position' => 1,
        ]);

        $size = Attribute::create([
            'name' => 'سایز',
            'slug' => 'size',
            'type' => 'select',
            'selection_mode' => 'multiple',
            'display_mode' => 'chip',
            'is_filterable' => true,
            'position' => 1,
        ]);

        $small = AttributeValue::create([
            'attribute_id' => $size->id,
            'value' => 'S',
            'slug' => 's',
            'position' => 1,
        ]);

        $medium = AttributeValue::create([
            'attribute_id' => $size->id,
            'value' => 'M',
            'slug' => 'm',
            'position' => 2,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name_fa' => 'تی‌شرت',
            'price' => 100000,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => 1,
            'attribute_values' => [
                $size->id => [$small->id, $medium->id],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::firstOrFail();

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $size->id,
            'attribute_value_id' => $small->id,
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $size->id,
            'attribute_value_id' => $medium->id,
        ]);
    }
}