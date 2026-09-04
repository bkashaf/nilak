<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailAttributeDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_shows_selected_attributes_and_quantity_controls(): void
    {
        $category = Category::create([
            'name' => 'کفش',
            'slug' => 'shoes',
            'status' => 1,
            'position' => 1,
        ]);

        $color = Attribute::create([
            'name' => 'رنگ',
            'slug' => 'color',
            'type' => 'select',
            'selection_mode' => 'multiple',
            'display_mode' => 'swatch',
            'is_filterable' => true,
            'position' => 1,
        ]);

        $size = Attribute::create([
            'name' => 'سایز',
            'slug' => 'size',
            'type' => 'select',
            'selection_mode' => 'multiple',
            'display_mode' => 'chip',
            'is_filterable' => true,
            'position' => 2,
        ]);

        $red = AttributeValue::create([
            'attribute_id' => $color->id,
            'value' => 'قرمز',
            'slug' => 'red',
            'color_hex' => '#FF0000',
            'position' => 1,
        ]);

        $size42 = AttributeValue::create([
            'attribute_id' => $size->id,
            'value' => '42',
            'slug' => '42',
            'position' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-TEST-01',
            'name' => 'کفش تست',
            'slug' => 'test-shoe',
            'price' => 300000,
            'stock' => 3,
            'is_active' => true,
        ]);

        ProductAttributeValue::create([
            'product_id' => $product->id,
            'attribute_id' => $color->id,
            'attribute_value_id' => $red->id,
        ]);

        ProductAttributeValue::create([
            'product_id' => $product->id,
            'attribute_id' => $size->id,
            'attribute_value_id' => $size42->id,
        ]);

        $response = $this->get(route('shop.product', $product->slug));

        $response->assertOk();
        $response->assertSee('قرمز');
        $response->assertSee('42');
        $response->assertSee('data-qty-action="increase"', false);
        $response->assertSee('data-qty-action="decrease"', false);
    }
}