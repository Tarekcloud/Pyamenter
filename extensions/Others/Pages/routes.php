<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\Pages\Livewire\Page;

Route::fallback(Page::class)->middleware('web')->name('extensions.others.pages');
