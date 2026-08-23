# scaffold-cart.ps1
$timestamp = (Get-Date).ToString('yyyyMMdd_HHmmss')
$backupDir = "backup_$timestamp"
New-Item -ItemType Directory -Path $backupDir | Out-Null

# فهرست فایل‌هایی که ممکن است بازنویسی شوند
$targets = @(
    "app\Domain\Cart\CartService.php",
    "app\Http\Controllers\Front\CartController.php",
    "app\Http\Controllers\Api\CartController.php",
    "routes\web.php",
    "resources\views\themes\cart.blade.php",
    "resources\views\themes\checkout.blade.php"
)

# پشتیبان‌گیری از فایل‌های موجود
foreach ($t in $targets) {
    if (Test-Path $t) {
        $dest = Join-Path $backupDir ($t -replace '[\\\/]','_')
        Copy-Item -Path $t -Destination $dest -Force
        Write-Host "Backed up $t -> $dest"
    }
}

# اطمینان از وجود دایرکتوری‌ها
New-Item -ItemType Directory -Force -Path "app\Domain\Cart" | Out-Null
New-Item -ItemType Directory -Force -Path "app\Http\Controllers\Front" | Out-Null
New-Item -ItemType Directory -Force -Path "resources\views\themes" | Out-Null

# نوشتن CartService (domain)
@"
<?php

namespace App\Domain\Cart;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    protected const SESSION_KEY = 'cart';

    public function all(): Collection
    {
        return collect(session(self::SESSION_KEY, []));
    }

    public function add(int \$productId, int \$qty = 1): Collection
    {
        \$cart = \$this->all();
        \$key = (string) \$productId;

        if (\$cart->has(\$key)) {
            \$item = \$cart->get(\$key);
            \$item['quantity'] = (\$item['quantity'] ?? 0) + \$qty;
            \$cart->put(\$key, \$item);
        } else {
            \$product = Product::find(\$productId);
            \$cart->put(\$key, [
                'product_id' => \$productId,
                'product' => \$product,
                'price' => \$product ? \$product->price : 0,
                'quantity' => \$qty,
            ]);
        }

        session([self::SESSION_KEY => \$cart]);

        return \$cart;
    }

    public function update(int \$productId, int \$qty): Collection
    {
        \$cart = \$this->all();
        \$key = (string) \$productId;

        if (\$cart->has(\$key)) {
            \$item = \$cart->get(\$key);
            \$item['quantity'] = [1, \$qty] | Sort-Object | Select-Object -Last 1
            \$cart->put(\$key, \$item);
            session([self::SESSION_KEY => \$cart]);
        }

        return \$cart;
    }

    public function remove(int \$productId): Collection
    {
        \$cart = \$this->all();
        \$key = (string) \$productId;

        if (\$cart->has(\$key)) {
            \$cart->forget(\$key);
            session([self::SESSION_KEY => \$cart]);
        }

        return \$cart;
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function total(): float
    {
        return \$this->all()->reduce(function (\$carry, \$item) {
            \$price = \$item['price'] ?? 0;
            \$qty = \$item['quantity'] ?? (\$item['qty'] ?? 0);
            return \$carry + (\$price * \$qty);
        }, 0.0);
    }

    public function items(): Collection
    {
        return \$this->all()->map(function (\$item) {
            \$product = \$item['product'] ?? null;
            \$quantity = \$item['quantity'] ?? (\$item['qty'] ?? 0);
            \$price = \$item['price'] ?? 0;
            return (object) [
                'product_id' => \$item['product_id'] ?? (\$product->id ?? null),
                'product' => \$product,
                'quantity' => \$quantity,
                'price' => \$price,
                'total' => \$price * \$quantity,
            ];
        })->values();
    }
}
"@ | Set-Content -Path "app\Domain\Cart\CartService.php" -Encoding UTF8

# نوشتن Front CartController
@"
<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Cart\CartService;
use App\Models\Product;

class CartController extends Controller
{
    protected CartService \$cartService;

    public function __construct(CartService \$cartService)
    {
        \$this->cartService = \$cartService;
    }

    public function index()
    {
        \$cart = \$this->cartService;
        \$items = \$cart->items();
        \$total = \$cart->total();

        if (view()->exists('themes.default.cart')) {
            return view('themes.default.cart', compact('items', 'total'));
        }

        return view('themes.cart', ['cart' => \$cart, 'items' => \$items, 'total' => \$total]);
    }

    public function add(Request \$request)
    {
        \$request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        \$product = Product::find(\$request->input('product_id'));
        if (! \$product || ! \$product->is_active) {
            return redirect()->back()->with('error', 'محصول نامعتبر است.');
        }

        \$qty = (int) \$request->input('qty', 1);
        \$this->cartService->add(\$product->id, \$qty);

        return redirect()->back()->with('success', 'محصول به سبد اضافه شد.');
    }

    public function update(Request \$request, \$productId)
    {
        \$request->validate(['quantity' => 'required|integer|min:1']);
        \$this->cartService->update((int)\$productId, (int)\$request->input('quantity'));
        return redirect()->back()->with('success', 'تعداد به‌روزرسانی شد.');
    }

    public function remove(Request \$request, \$productId)
    {
        \$this->cartService->remove((int)\$productId);
        return redirect()->back()->with('success', 'آیتم حذف شد.');
    }

    public function checkout(Request \$request)
    {
        \$cart = \$this->cartService;
        if (\$cart->all()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید خالی است.');
        }

        \$this->cartService->clear();

        return redirect()->route('shop.index')->with('success', 'سفارش ثبت شد (نمونه).');
    }
}
"@ | Set-Content -Path "app\Http\Controllers\Front\CartController.php" -Encoding UTF8

# نوشتن Api CartController (بازنویسی)
@"
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Cart\CartService;

class CartController extends Controller
{
    protected CartService \$cartService;

    public function __construct(CartService \$cartService)
    {
        \$this->cartService = \$cartService;
    }

    public function index()
    {
        \$items = \$this->cartService->items();
        return response()->json(['items' => \$items, 'total' => \$this->cartService->total()]);
    }

    public function add(Request \$request)
    {
        \$productId = (int) \$request->product_id;
        \$quantity = (int) (\$request->quantity ?? 1);
        \$cart = \$this->cartService->add(\$productId, \$quantity);
        return response()->json(['message' => 'added', 'cart' => \$cart]);
    }

    public function update(Request \$request, \$productId)
    {
        \$quantity = (int) (\$request->quantity ?? 1);
        \$cart = \$this->cartService->update((int)\$productId, \$quantity);
        return response()->json(['message' => 'updated', 'cart' => \$cart]);
    }

    public function remove(Request \$request, \$productId)
    {
        \$cart = \$this->cartService->remove((int)\$productId);
        return response()->json(['message' => 'removed', 'cart' => \$cart]);
    }

    public function clear()
    {
        \$this->cartService->clear();
        return response()->json(['message' => 'cleared']);
    }
}
"@ | Set-Content -Path "app\Http\Controllers\Api\CartController.php" -Encoding UTF8

# افزودن روت‌ها به انتهای routes/web.php
Add-Content -Path "routes\web.php" -Value "`nuse App\Http\Controllers\Front\CartController;`nRoute::post('/cart/add', [CartController::class, 'add'])->name('cart.add');`nRoute::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');`nRoute::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');`nRoute::get('/cart', [CartController::class, 'index'])->name('cart.index');`nRoute::post('/checkout/process', [CartController::class, 'checkout'])->name('checkout.process');`n"

# نوشتن viewها با UTF-8 اصلاح‌شده
# cart.blade.php
@"
@php \$cart = session('cart', collect()); @endphp
{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/cart.blade.php --}}
@extends('themes.default.layouts.shop')

@section('content')

<div class='d-flex justify-content-between align-items-center mb-4'>
    <h2>سبد خرید</h2>
    <a href='{{ route('shop.index') }}' class='btn btn-secondary'>بازگشت به فروشگاه</a>
</div>

@if(session('success'))
    <div class='alert alert-success'>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class='alert alert-danger'>{{ session('error') }}</div>
@endif

@if(\$cart->isEmpty())
    <div class='alert alert-info'>سبد خرید شما خالی است.</div>
@else

<table class='table table-bordered table-striped'>
    <thead>
        <tr>
            <th style='width: 80px'>تصویر</th>
            <th>محصول</th>
            <th>قیمت واحد</th>
            <th style='width: 120px'>تعداد</th>
            <th>قیمت کل</th>
            <th style='width: 100px'>عملیات</th>
        </tr>
    </thead>
    <tbody>

        @foreach(\$cart->items() as \$item)
            <tr>
                <td>
                    @if(\$item->product && \$item->product->primaryImage)
                        <img src='{{ asset('storage/'.\$item->product->primaryImage->path) }}' style='width:60px; height:60px; object-fit:cover;'>
                    @else
                        <span class='text-muted'>—</span>
                    @endif
                </td>
                <td>{{ \$item->product->name ?? '—' }}</td>
                <td>{{ number_format(\$item->price) }} تومان</td>
                <td>
                    <form action='{{ route('cart.update', \$item->product_id) }}' method='POST'>
                        @csrf
                        @method('PUT')
                        <input type='number' name='quantity' value='{{ \$item->quantity }}' min='1' class='form-control form-control-sm' style='width:70px; display:inline-block;'>
                        <button class='btn btn-sm btn-primary mt-1'>بروزرسانی</button>
                    </form>
                </td>
                <td>{{ number_format(\$item->total) }} تومان</td>
                <td>
                    <form action='{{ route('cart.remove', \$item->product_id) }}' method='POST' onsubmit='return confirm(\"حذف این آیتم؟\");'>
                        @csrf
                        @method('DELETE')
                        <button class='btn btn-sm btn-danger'>حذف</button>
                    </form>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>

<div class='card mt-4'>
    <div class='card-body d-flex justify-content-between align-items-center'>
        <h4>جمع کل: {{ number_format(\$cart->total()) }} تومان</h4>
        <div>
            <a href='{{ route('shop.index') }}' class='btn btn-secondary'>ادامه خرید</a>
            <a href='{{ route('checkout.index') }}' class='btn btn-success'>تسویه حساب</a>
        </div>
    </div>
</div>

@endif

@endsection
"@ | Set-Content -Path "resources\views\themes\cart.blade.php" -Encoding UTF8

# checkout.blade.php
@"
@php \$cart = session('cart', collect()); @endphp
{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/checkout.blade.php --}}
@extends('themes.default.layouts.shop')

@section('content')

<div class='d-flex justify-content-between align-items-center mb-4'>
    <h2>تسویه حساب</h2>
    <a href='{{ route('cart.index') }}' class='btn btn-secondary'>بازگشت به سبد خرید</a>
</div>

@if(\$cart->isEmpty())
    <div class='alert alert-info'>سبد خرید شما خالی است.</div>
@else

<div class='row'>

    <div class='col-md-7'>
        <div class='card mb-4'>
            <div class='card-header'>اطلاعات گیرنده</div>
            <div class='card-body'>
                <form action='{{ route('checkout.process') }}' method='POST'>
                    @csrf
                    <div class='mb-3'>
                        <label class='form-label'>نام و نام خانوادگی *</label>
                        <input type='text' name='name' class='form-control' value='{{ old('name') }}' required>
                    </div>
                    <div class='mb-3'>
                        <label class='form-label'>شماره موبایل *</label>
                        <input type='text' name='mobile' class='form-control' value='{{ old('mobile') }}' required>
                    </div>
                    <div class='mb-3'>
                        <label class='form-label'>آدرس کامل *</label>
                        <textarea name='address' class='form-control' rows='3' required>{{ old('address') }}</textarea>
                    </div>
                    <div class='mb-3'>
                        <label class='form-label'>توضیحات سفارش (اختیاری)</label>
                        <textarea name='notes' class='form-control' rows='2'>{{ old('notes') }}</textarea>
                    </div>
                    <button type='submit' class='btn btn-success btn-lg w-100'>ثبت سفارش و ادامه پرداخت</button>
                </form>
            </div>
        </div>
    </div>

    <div class='col-md-5'>
        <div class='card'>
            <div class='card-header'>خلاصه سفارش</div>
            <div class='card-body'>
                <table class='table'>
                    <tbody>
                        @foreach(\$cart->items() as \$item)
                            <tr>
                                <td>{{ \$item->product->name ?? '—' }}</td>
                                <td>{{ \$item->quantity }} عدد</td>
                                <td>{{ number_format(\$item->total) }} تومان</td>
                            </tr>
                        @endforeach
                        <tr class='table-secondary'>
                            <td colspan='2'><strong>جمع کل</strong></td>
                            <td><strong>{{ number_format(\$cart->total()) }} تومان</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endif

@endsection
"@ | Set-Content -Path "resources\views\themes\checkout.blade.php" -Encoding UTF8

Write-Host "Files written. Now running composer dump-autoload and artisan clears..."
composer dump-autoload
php artisan route:clear
php artisan view:clear
php artisan cache:clear

Write-Host "Done. Backups are in $backupDir"
