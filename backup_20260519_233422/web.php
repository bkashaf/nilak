<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('themes.default.home');
});

// 🔥 مسیرهای ورود
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// 🔥 خروج از سیستم
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// بارگذاری مسیرهای پنل ادمین زیر پیشوند /admin و نام admin.
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    require __DIR__.'/admin.php';
});
