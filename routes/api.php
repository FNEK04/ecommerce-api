<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Товары (публичные)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Callback для оплаты (публичный)
Route::post('/payment/{paymentUrl}', [OrderController::class, 'paymentCallback']);

// Защищенные маршруты
Route::middleware('auth:sanctum')->group(function () {
    // Авторизация
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Корзина
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::delete('/cart/remove', [CartController::class, 'remove']);
    
    // Заказы
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/orders/{order}/paid', [OrderController::class, 'markAsPaid']);
});