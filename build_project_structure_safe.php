<?php
/**
 * build_project_structure_safe.php
 *
 * اسکریپت امن برای ساخت پوشه‌ها و فایل‌های مورد نیاز پروژه
 * بدون بازنویسی هیچ فایل موجود.
 */

$basePath = __DIR__;

$items = [

    // 1) محصولات - Catalog
    [
        'path' => 'app/Http/Controllers/Admin/ProductController.php',
        'type' => 'controller',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    ['path' => 'resources/views/admin/products/index.blade.php',  'type' => 'blade'],
    ['path' => 'resources/views/admin/products/create.blade.php', 'type' => 'blade'],
    ['path' => 'resources/views/admin/products/edit.blade.php',   'type' => 'blade'],

    // 2) دسته‌بندی‌ها - Categories
    [
        'path' => 'app/Http/Controllers/Admin/CategoryController.php',
        'type' => 'controller',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    ['path' => 'resources/views/admin/categories/index.blade.php',  'type' => 'blade'],
    ['path' => 'resources/views/admin/categories/create.blade.php', 'type' => 'blade'],
    ['path' => 'resources/views/admin/categories/edit.blade.php',   'type' => 'blade'],

    // 3) صفحه فروشگاه - Shop
    ['path' => 'resources/views/themes/shop.blade.php', 'type' => 'blade'],

    // 4) صفحه محصول تکی - Product Detail
    ['path' => 'resources/views/themes/product.blade.php', 'type' => 'blade'],

    // 5) سبد خرید - Cart
    [
        'path' => 'app/Domain/Cart/CartService.php',
        'type' => 'domain',
        'namespace' => 'App\Domain\Cart',
    ],
    ['path' => 'resources/views/themes/cart.blade.php', 'type' => 'blade'],

    // 6) سفارش - Order
    [
        'path' => 'app/Domain/Order/OrderService.php',
        'type' => 'domain',
        'namespace' => 'App\Domain\Order',
    ],
    ['path' => 'resources/views/themes/checkout.blade.php', 'type' => 'blade'],

    // 7) پرداخت - Payment
    [
        'path' => 'app/Domain/Payment/PaymentService.php',
        'type' => 'domain',
        'namespace' => 'App\Domain\Payment',
    ],

    // 8) گزارش‌ها و تنظیمات - Reports & Settings
    ['path' => 'resources/views/admin/reports/index.blade.php',  'type' => 'blade'],
    ['path' => 'resources/views/admin/settings/index.blade.php', 'type' => 'blade'],
];

function makeDirIfNotExists(string $dir)
{
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "📁 پوشه ساخته شد: $dir\n";
    }
}

function createControllerStub(string $fullPath, string $namespace)
{
    $className = pathinfo($fullPath, PATHINFO_FILENAME);

    $stub = <<<PHP
<?php

namespace {$namespace};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$className} extends Controller
{
    // TODO: پیاده‌سازی متدهای کنترلر
}

PHP;

    file_put_contents($fullPath, $stub);
}

function createDomainStub(string $fullPath, string $namespace)
{
    $className = pathinfo($fullPath, PATHINFO_FILENAME);

    $stub = <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    // TODO: پیاده‌سازی منطق دامنه ({$className})
}

PHP;

    file_put_contents($fullPath, $stub);
}

function createBladeStub(string $fullPath)
{
    $relative = str_replace('\\', '/', $fullPath);
    $stub = <<<BLADE
{{-- View: {$relative} --}}
@extends('layouts.admin')

@section('content')
    <h1>در حال توسعه...</h1>
@endsection

BLADE;

    file_put_contents($fullPath, $stub);
}

foreach ($items as $item) {
    $path = $item['path'];
    $type = $item['type'];

    $fullPath = $basePath . DIRECTORY_SEPARATOR . $path;
    $dir = dirname($fullPath);

    makeDirIfNotExists($dir);

    if (file_exists($fullPath)) {
        echo "✅ فایل موجود است (تغییری داده نشد): {$path}\n";
        continue;
    }

    switch ($type) {
        case 'controller':
            createControllerStub($fullPath, $item['namespace']);
            echo "📄 کنترلر ساخته شد: {$path}\n";
            break;

        case 'domain':
            createDomainStub($fullPath, $item['namespace']);
            echo "📄 کلاس دامنه ساخته شد: {$path}\n";
            break;

        case 'blade':
            createBladeStub($fullPath);
            echo "📄 ویو Blade ساخته شد: {$path}\n";
            break;

        default:
            // حالت پیش‌فرض: فایل خالی
            file_put_contents($fullPath, '');
            echo "📄 فایل خالی ساخته شد: {$path}\n";
            break;
    }
}

echo "\n🎉 اتمام کار: ساختار مورد نیاز بدون بازنویسی فایل‌های موجود ایجاد شد.\n";
