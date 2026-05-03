<?php

namespace Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter;

use Livewire\Component;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;
use Paymenter\Extensions\Others\Helpcenter\Models\Category;
use Paymenter\Extensions\Others\Helpcenter\Models\FAQ;
use Paymenter\Extensions\Others\Helpcenter\Models\Link;

class Index extends Component
{
    public $search = '';
    public $category = null;
    public $limit = null;

    protected $queryString = ['search', 'category'];

    public function mount($limit = null)
    {
        $this->limit = $limit;
        $this->search = request('search', '');
    }

    public function selectCategory($categoryId = null)
    {
        $this->category = $categoryId;
    }

    public function render()
    {
        $search = $this->search;

        $query = Article::with('category')
            ->where('is_active', true)
            ->latest('published_at');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($this->category !== null) {
            $query->where('category_id', $this->category);
        }

        $articles = $this->limit ? $query->take($this->limit)->get() : $query->get();

        $categories = Category::withCount([
                'articles' => function ($q) {
                    $q->where('is_active', true);
                }
            ])
            ->when(!empty($search), function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->get();

        $globalFaqs = FAQ::whereNull('article_id')
            ->where('is_active', true)
            ->when(!empty($search), function ($q) use ($search) {
                $q->where('question', 'like', '%' . $search . '%')
                  ->orWhere('answer', 'like', '%' . $search . '%');
            })
            ->orderBy('sort_order')
            ->get();

        $links = Link::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('helpcenter::index', [
            'articles' => $articles,
            'categories' => $categories,
            'globalFaqs' => $globalFaqs,
            'links' => $links,
            'search' => $search,
        ]);
    }
}
