<?php

use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Index;
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Show;
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\CategoryPage;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {

Route::post('/help/article/{article:slug}/vote/{type}', function (Request $request, Article $article, string $type) {
    if ($type === 'yes') {
        $article->increment('helpful_yes');
    } elseif ($type === 'no') {
        $article->increment('helpful_no');
    }

    $request->session()->put("voted_article_{$article->id}", true);

    return back();
})->name('helpcenter.vote');

    Route::get('/help', Index::class)->name('helpcenter.index');
    Route::get('/help/article/{article:slug}', Show::class)->name('helpcenter.show');
    Route::get('/help/category/{category:slug}', CategoryPage::class)->name('helpcenter.category');
});
