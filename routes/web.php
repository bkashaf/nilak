<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Front\ShopController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\OrderTrackingController;
use App\Http\Controllers\Front\PaymentCallbackController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Installer\InstallerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Front\OrderController; // ✅ اصلاح شد

/*
|--------------------------------------------------------------------------
| Web Routes (Frontend)
|--------------------------------------------------------------------------
*/

// صفحه اصلی
Route::get('/', [HomeController::class, 'index'])->name('home');

// فروشگاه
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.product');

// پیگیری سفارش
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])
    ->name('order.tracking');

Route::post('/order-tracking', [OrderTrackingController::class, 'show'])
    ->name('order.tracking.submit');

// کال‌بک زرین‌پال
Route::get('/payment/zarinpal/callback/{payment}', [PaymentCallbackController::class, 'zarinpal'])
    ->name('payment.zarinpal.callback');

// صفحات داینامیک
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// تماس با ما
Route::post('/contact/submit', [ContactController::class, 'submit'])
    ->middleware('auth')
    ->name('contact.submit');

// سبد خرید
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

// تسویه حساب
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
})->middleware(['auth', 'profile.complete'])->name('checkout.index');

Route::post('/checkout/process', [CartController::class, 'checkout'])
    ->middleware(['auth', 'profile.complete'])
    ->name('checkout.process');

// احراز هویت
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// پروفایل کاربری
Route::middleware('auth')->group(function () {
    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile.edit');
    Route::put('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
});

// سفارش‌های کاربر
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {

    // لیست سفارش‌ها
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');

    // جزئیات سفارش
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // ارسال رسید بانکی
    Route::post('/receipt/{payment}', [OrderController::class, 'uploadReceipt'])->name('receipt.upload');
});

// تغییر زبان
Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fa', 'en'], true), 404);
    session(['locale' => $locale]);
    return redirect()->back();
})->name('language.switch');

// نصب سیستم
Route::prefix('install')->name('install.')->middleware('installer.access')->group(function () {
    require __DIR__.'/install.php';
});

// پنل مدیریت
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    require __DIR__.'/admin.php';
});
