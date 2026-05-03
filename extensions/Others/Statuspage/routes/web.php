<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\Statuspage\Livewire\Index;
use Paymenter\Extensions\Others\Statuspage\Livewire\Show;

Route::get('/test-statuspage', function() { return 'OK'; });

Route::group(['middleware' => ['web']], function () {
    Route::get('/status', Index::class)->name('statuspage.index');
    Route::get('/status/incident/{incident}', Show::class)->name('statuspage.show');
});
