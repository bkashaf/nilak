<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttributeValue;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // دسته‌ها (idempotent)
            $cat1 = Category::firstOrCreate(
                ['slug' => 'lebas'],
                [
                    'name' => 'لباس',
                    'description' => 'دستهٔ نمونه برای لباس',
                    'status' => 1,
                    'position' => 1,
                ]
            );

            $cat2 = Category::firstOrCreate(
                ['slug' => 'kafsh'],
                [
                    'name' => 'کفش',
                    'description' => 'دستهٔ نمونه برای کفش',
                    'status' => 1,
                    'position' => 2,
                ]
            );

            // ویژگی‌ها (idempotent)
            $attrColor = Attribute::firstOrCreate(
                ['slug' => 'color'],
                [
                    'name' => 'رنگ',
                    'type' => 'select',
                    'is_filterable' => true,
                    'is_required' => false,
                    'position' => 1,
                ]
            );

            $attrSize = Attribute::firstOrCreate(
                ['slug' => 'size'],
                [
                    'name' => 'سایز',
                    'type' => 'select',
                    'is_filterable' => true,
                    'is_required' => false,
                    'position' => 2,
                ]
            );

            // مقادیر ویژگی‌ها (idempotent)
            $red = AttributeValue::firstOrCreate(
                ['attribute_id' => $attrColor->id, 'value' => 'قرمز'],
                [
                    'slug' => Str::slug('قرمز'),
                    'position' => 1,
                ]
            );

            $blue = AttributeValue::firstOrCreate(
                ['attribute_id' => $attrColor->id, 'value' => 'آبی'],
                [
                    'slug' => Str::slug('آبی'),
                    'position' => 2,
                ]
            );

            $sizeM = AttributeValue::firstOrCreate(
                ['attribute_id' => $attrSize->id, 'value' => 'M'],
                [
                    'slug' => 'm',
                    'position' => 1,
                ]
            );

            $sizeL = AttributeValue::firstOrCreate(
                ['attribute_id' => $attrSize->id, 'value' => 'L'],
                [
                    'slug' => 'l',
                    'position' => 2,
                ]
            );

            // محصولات نمونه (idempotent by sku)
            $p1 = Product::updateOrCreate(
                ['sku' => 'LB-001'],
                [
                    'category_id' => $cat1->id,
                    'name' => 'تی شرت نمونه',
                    'slug' => 't-shirt-sample',
                    'short_description' => 'تی شرت راحت و سبک',
                    'description' => 'توضیحات کامل تی شرت نمونه.',
                    'price' => 199000,
                    'compare_price' => 249000,
                    'stock' => 50,
                    'is_active' => true,
                    'is_featured' => true,
                    'meta' => ['brand' => 'نمونه'],
                ]
            );

            $p2 = Product::updateOrCreate(
                ['sku' => 'KF-001'],
                [
                    'category_id' => $cat2->id,
                    'name' => 'کفش اسپرت نمونه',
                    'slug' => 'sport-shoe-sample',
                    'short_description' => 'کفش اسپرت راحت',
                    'description' => 'توضیحات کامل کفش اسپرت نمونه.',
                    'price' => 499000,
                    'compare_price' => null,
                    'stock' => 20,
                    'is_active' => true,
                    'is_featured' => false,
                    'meta' => ['brand' => 'نمونه'],
                ]
            );

            // تصاویر نمونه (idempotent by product_id + path)
            ProductImage::firstOrCreate(
                ['product_id' => $p1->id, 'path' => 'products/tshirt-1.jpg'],
                [
                    'alt' => 'تی شرت نمونه',
                    'position' => 0,
                    'is_primary' => true,
                ]
            );

            ProductImage::firstOrCreate(
                ['product_id' => $p2->id, 'path' => 'products/shoe-1.jpg'],
                [
                    'alt' => 'کفش اسپرت نمونه',
                    'position' => 0,
                    'is_primary' => true,
                ]
            );

            // اتصال محصولات به مقادیر ویژگی (idempotent)
            ProductAttributeValue::firstOrCreate(
                [
                    'product_id' => $p1->id,
                    'attribute_id' => $attrColor->id,
                    'attribute_value_id' => $red->id,
                ],
                ['value_text' => null]
            );

            ProductAttributeValue::firstOrCreate(
                [
                    'product_id' => $p1->id,
                    'attribute_id' => $attrSize->id,
                    'attribute_value_id' => $sizeM->id,
                ],
                ['value_text' => null]
            );

            ProductAttributeValue::firstOrCreate(
                [
                    'product_id' => $p2->id,
                    'attribute_id' => $attrColor->id,
                    'attribute_value_id' => $blue->id,
                ],
                ['value_text' => null]
            );

            ProductAttributeValue::firstOrCreate(
                [
                    'product_id' => $p2->id,
                    'attribute_id' => $attrSize->id,
                    'attribute_value_id' => $sizeL->id,
                ],
                ['value_text' => null]
            );
        });
    }
}
