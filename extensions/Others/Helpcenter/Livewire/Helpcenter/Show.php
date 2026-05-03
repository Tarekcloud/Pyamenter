<?php

namespace Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter;

use Livewire\Component;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;

class Show extends Component
{
    public Article $article;

    public function mount(Article $article)
    {
        if (!$article->is_active) {
        abort(404); 
        }
        
        $this->article = $article;
    }

    public function render()
    {
        $faqs = $this->article->faqs()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('helpcenter::show', [
            'article' => $this->article,
            'faqs' => $faqs,
        ]);
    }
}
