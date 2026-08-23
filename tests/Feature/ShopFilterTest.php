<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_filters_products_by_attribute_value(): void
    {
        $category = Category::create([
            'name' => 'لباس',
            'slug' => 'filter-clothing',
            'status' => 1,
            'position' => 1,
        ]);
        $attribute = Attribute::create([
            'name' => 'رنگ',
            'slug' => 'color',
            'type' => 'select',
            'is_filterable' => true,
            'position' => 1,
        ]);
        $red = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'قرمز',
            'slug' => 'red',
            'position' => 1,
        ]);
        $firstProduct = Product::create([
            'category_id' => $category->id,
            'sku' => 'FILTER-RED',
            'name' => 'محصول قرمز',
            'slug' => 'filter-red',
            'price' => 1000,
            'stock' => 2,
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'sku' => 'FILTER-BLUE',
            'name' => 'محصول دیگر',
            'slug' => 'filter-other',
            'price' => 1000,
            'stock' => 2,
            'is_active' => true,
        ]);
        ProductAttributeValue::create([
            'product_id' => $firstProduct->id,
            'attribute_id' => $attribute->id,
            'attribute_value_id' => $red->id,
        ]);

        $response = $this->get('/shop?attributes[color]=' . $red->id);

        $response->assertOk();
        $response->assertSee('محصول قرمز');
        $response->assertDontSee('محصول دیگر');
    }

    public function test_shop_sorts_and_filters_by_price_range(): void
    {
        $category = Category::create([
            'name' => 'لباس',
            'slug' => 'sort-clothing',
            'status' => 1,
            'position' => 1,
        ]);
        Product::create([
            'category_id' => $category->id,
            'sku' => 'SORT-LOW',
            'name' => 'محصول ارزان',
            'slug' => 'sort-low',
            'price' => 1000,
            'stock' => 1,
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'sku' => 'SORT-HIGH',
            'name' => 'محصول گران',
            'slug' => 'sort-high',
            'price' => 5000,
            'stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->get('/shop?sort=price_desc&price_min=4000');

        $response->assertOk();
        $response->assertSee('محصول گران');
        $response->assertDontSee('محصول ارزان');
    }
}
