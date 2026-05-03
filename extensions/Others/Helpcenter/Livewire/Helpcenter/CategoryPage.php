<?php

namespace Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter;

use Livewire\Component;
use Paymenter\Extensions\Others\Helpcenter\Models\Category;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;

class CategoryPage extends Component
{
    public $category;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        $articles = Article::where('category_id', $this->category->id)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->get();

        return view('helpcenter::category', [
            'category' => $this->category,
            'articles' => $articles,
        ]);
    }
}
