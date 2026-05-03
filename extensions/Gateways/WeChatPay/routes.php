<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\WeChatPay\WeChatPay;

Route::post('/extensions/wechatpay/webhook', [WeChatPay::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('extensions.gateways.wechatpay.webhook');
Route::get('/extensions/wechatpay/status/{invoice}', [WeChatPay::class, 'checkPaymentStatus'])->middleware(['web'])->name('extensions.gateways.wechatpay.status');
