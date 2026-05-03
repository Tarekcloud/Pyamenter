<?php

namespace Paymenter\Extensions\Others\Pages\Livewire;

use App\Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Paymenter\Extensions\Others\Pages\Models\Page as ModelsPage;

class Page extends Component
{
    public ModelsPage $page;

    public function mount($fallbackPlaceholder)
    {
        // Validate if page exists
        $this->page = ModelsPage::where('slug', $fallbackPlaceholder)->firstOrFail();
        if (!$this->page->visible) {
            abort(404);
        }
        // Validate if user is logged in
        if ($this->page->visibility == 'client' && !auth()->check()) {
            abort(404);
        }
        if ($this->page->visibility == 'admin' && (!auth()->check() || is_null(auth()->user()->role))) {
            abort(404);
        }
    }

    public function render()
    {
        return view('others.pages::page', [
            'page' => $this->page,
        ])->layout('layouts.app', [
            'title' => $this->page->title,
            'description' => $this->page->description,
            'image' => $this->page->image ? Storage::url($this->page->image) : null,
            'sidebar' => $this->page->navigation == 'dashboard' ? true : false,
        ]);
    }
}
