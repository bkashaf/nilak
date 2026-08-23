<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Front\ShopController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\HomeController;   // ← اضافه شد
use App\Http\Controllers\Front\OrderTrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Frontend routes: home, shop.index, shop.product, cart.index, checkout.index
|
*/

// صفحه اصلی → HomeController
Route::get('/', [HomeController::class, 'index'])->name('home');

// صفحه فروشگاه
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

// صفحه محصول (نام‌دار)
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.product');

Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('orders.track.form');
Route::post('/order-tracking', [OrderTrackingController::class, 'show'])->name('orders.track');

// مسیرهای سبد خرید (CartController فرانت)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

// مسیر تسویه حساب (Checkout)
Route::get('/checkout', function () {
    $cart = session('cart', collect());
    $paymentMethods = \App\Models\PaymentMethod::query()
        ->where('is_active', true)
        ->orderBy('id')
        ->get();
    if (view()->exists('themes.default.checkout')) {
        return view('themes.default.checkout', compact('cart', 'paymentMethods'));
    }
    if (view()->exists('themes.checkout')) {
        return view('themes.checkout', compact('cart', 'paymentMethods'));
    }
    abort(404);
})->name('checkout.index');

Route::post('/checkout/process', [CartController::class, 'checkout'])
    ->middleware('auth')
    ->name('checkout.process');

// مسیرهای احراز هویت
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fa', 'en'], true), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

// بارگذاری مسیرهای پنل ادمین زیر پیشوند /admin و نام admin.
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    require __DIR__.'/admin.php';
});
