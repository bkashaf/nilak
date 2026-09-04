<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BankReceiptController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
});

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
});

Route::prefix('bank-receipts')->name('bank-receipts.')->group(function () {
    Route::get('/{bankReceipt}', [BankReceiptController::class, 'show'])->name('show');
    Route::post('/{bankReceipt}/approve', [BankReceiptController::class, 'approve'])->name('approve');
    Route::post('/{bankReceipt}/reject', [BankReceiptController::class, 'reject'])->name('reject');
});

Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
    Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
    Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('update');
});

Route::prefix('attributes')->name('attributes.')->group(function () {
    Route::get('/', [AttributeController::class, 'index'])->name('index');
    Route::post('/', [AttributeController::class, 'store'])->name('store');
    Route::put('/{attribute}', [AttributeController::class, 'update'])->name('update');
    Route::delete('/{attribute}', [AttributeController::class, 'destroy'])->name('destroy');

    Route::post('/{attribute}/values', [AttributeController::class, 'storeValue'])->name('values.store');
    Route::put('/{attribute}/values/{value}', [AttributeController::class, 'updateValue'])->name('values.update');
    Route::delete('/{attribute}/values/{value}', [AttributeController::class, 'destroyValue'])->name('values.destroy');
});

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('/create', [PageController::class, 'create'])->name('create');
    Route::post('/', [PageController::class, 'store'])->name('store');
    Route::post('/upload-image', [PageController::class, 'uploadImage'])->name('upload-image');
    Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
    Route::put('/{page}', [PageController::class, 'update'])->name('update');
    Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
});

Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.pages.index');
    })->name('index');
});

Route::prefix('sliders')->name('sliders.')->group(function () {
    Route::get('/', [SliderController::class, 'index'])->name('index');
    Route::post('/', [SliderController::class, 'store'])->name('store');
    Route::put('/{slider}', [SliderController::class, 'update'])->name('update');
    Route::delete('/{slider}', [SliderController::class, 'destroy'])->name('destroy');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('index');
});

Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/', [SettingsController::class, 'update'])->name('update');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
});