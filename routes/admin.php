<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
});

// مدیریت کاربران
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// مدیریت صفحات
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/', function () {
        return 'لیست صفحات';
    })->name('index');
});

// مدیریت منو
Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/', function () {
        return 'مدیریت منو';
    })->name('index');
});

// گزارش‌ها
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', function () {
        return 'گزارش‌ها';
    })->name('index');
});

// تنظیمات
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', function () {
        return 'تنظیمات سیستم';
    })->name('index');
});

// مدیریت دسته‌ها
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
});

// مدیریت محصولات
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
});
