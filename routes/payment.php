<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payments\PaymentController;

Route::prefix('payment')->middleware(['auth:customer'])->group(function () {
    Route::get('/pay/{gateway}', [PaymentController::class, 'pay'])->name('payment.pay');
    Route::get('/result', [PaymentController::class, 'result'])->name('payment.result');
});

Route::prefix('payment')->group(function () {
    Route::get('/idpay/callback', [PaymentController::class, 'idpayCallback'])->name('payment.idpay.callback');
    Route::get('/zarinpal/callback', [PaymentController::class, 'zarinpalCallback'])->name('payment.zarinpal.callback');
});

