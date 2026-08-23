<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);

use App\Http\Controllers\Api\CategoryController;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

use App\Http\Controllers\Api\AttributeController;

Route::get('/attributes', [AttributeController::class, 'index']);
Route::get('/attributes/{id}', [AttributeController::class, 'show']);

use App\Http\Controllers\Api\CartController;

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/remove', [CartController::class, 'remove']);
Route::post('/cart/clear', [CartController::class, 'clear']);

use App\Http\Controllers\Api\AuthController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

use App\Http\Controllers\Api\OrderController;

use App\Http\Controllers\Api\PaymentController;


use App\Http\Controllers\Api\BankReceiptController;

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/orders', [OrderController::class, 'index']);
	Route::post('/orders', [OrderController::class, 'store']);
	Route::get('/orders/{id}', [OrderController::class, 'show']);
	Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
	Route::post('/payment/verify', [PaymentController::class, 'verify']);
	Route::post('/payment/upload-receipt', [BankReceiptController::class, 'upload']);
	Route::post('/payment/approve-receipt', [BankReceiptController::class, 'approve'])
		->middleware('admin');
});
