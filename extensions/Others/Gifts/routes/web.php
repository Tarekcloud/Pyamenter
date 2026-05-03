<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\Gifts\Livewire\Gifts\Redeem;
use Paymenter\Extensions\Others\Gifts\Livewire\Gifts\RedeemDirect;

Route::middleware(['web'])->group(function () {
    Route::get('gift/{code}', RedeemDirect::class)->name('gifts.redeem.direct');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('gifts')->name('gifts.')->group(function () {
        Route::get('redeem', Redeem::class)->name('redeem');
        Route::get('redeem/{code}', Redeem::class)->name('redeem.code');
    });
});
