<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\ReferralSystem\Livewire\Referrals\Dashboard; //548fdcc170237119fce11c345ee45c29

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/account/referrals', Dashboard::class)->name('referrals.dashboard');
});
