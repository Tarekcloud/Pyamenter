<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\Alipay\Alipay;

Route::post('/extensions/alipay/webhook', [Alipay::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('extensions.gateways.alipay.webhook');
Route::get('/extensions/alipay/status/{invoice}', [Alipay::class, 'checkPaymentStatus'])->middleware(['web'])->name('extensions.gateways.alipay.status');
