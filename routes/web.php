<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel E-commerce API' => 'Running'];
});

// Публичная страница для оплаты
Route::get('/payment/{paymentUrl}', function ($paymentUrl) {
    return view('payment', compact('paymentUrl'));
});