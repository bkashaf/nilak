<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Installer\InstallerController;

Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
Route::get('/resume', [InstallerController::class, 'resume'])->name('resume');
Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
Route::get('/database', [InstallerController::class, 'database'])->name('database');
Route::post('/database-test', [InstallerController::class, 'databaseTest'])->name('database.test');
Route::get('/store-settings', [InstallerController::class, 'storeSettings'])->name('store-settings');
Route::post('/store-settings', [InstallerController::class, 'storeSettingsSave'])->name('store-settings.save');
Route::get('/summary', [InstallerController::class, 'summary'])->name('summary');
Route::post('/run', [InstallerController::class, 'run'])->name('run');
