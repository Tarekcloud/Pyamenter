<?php

namespace Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter;

use Livewire\Component;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;

class Widget extends Component
{
    public function mount()
    {
        if (Article::where('is_active', true)->where('published_at', '<=', now())->count() == 0) {
            abort(404);
        }
    }

    public function render()
    {
        return view('helpcenter::widget', [
            'articles' => Article::where('is_active', true)
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->get(),
        ]);
    }
}
