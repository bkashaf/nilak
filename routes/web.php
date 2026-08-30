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
use App\Http\Controllers\ContactController;   // ← اضافه شد

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Frontend routes: home, shop.index, shop.product, cart.index, checkout.index
|--------------------------------------------------------------------------
*/

// صفحه اصلی
Route::get('/', [HomeController::class, 'index'])->name('home');

// صفحه فروشگاه
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

// صفحه محصول
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.product');

// پیگیری سفارش
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('orders.track.form');
Route::post('/order-tracking', [OrderTrackingController::class, 'show'])->name('orders.track');

// صفحات داینامیک
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// فرم تماس با ما ← جدید
Route::post('/contact/submit', [ContactController::class, 'submit'])
    ->middleware('auth')
    ->name('contact.submit');

// کال‌بک زرین‌پال
Route::get('/payment/zarinpal/callback/{payment}', [PaymentCallbackController::class, 'zarinpal'])
    ->name('payment.zarinpal.callback');

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

// تغییر زبان
Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fa', 'en'], true), 404);
    session(['locale' => $locale]);
    return redirect()->back();
})->name('language.switch');

// نصب سیستم
Route::prefix('install')->name('install.')->middleware('installer.access')->group(function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
    Route::get('/resume', [InstallerController::class, 'resume'])->name('resume');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database-test', [InstallerController::class, 'databaseTest'])->name('database.test');
    Route::get('/store-settings', [InstallerController::class, 'storeSettings'])->name('store-settings');
    Route::post('/store-settings', [InstallerController::class, 'storeSettingsSave'])->name('store-settings.save');
    Route::get('/summary', [InstallerController::class, 'summary'])->name('summary');
    Route::post('/run', [InstallerController::class, 'run'])->name('run');
});

// پنل مدیریت
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    require __DIR__.'/admin.php';
});
