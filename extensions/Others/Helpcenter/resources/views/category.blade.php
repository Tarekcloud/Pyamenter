<div style="display:flex; min-height:100vh; background-color:var(--background-primary);">

    

    <style>
    li a:hover span {
        transform: translateX(5px); 
    }
    </style>

    {{-- Main Content Area --}}
    <div style="flex-grow:1; padding:2rem; background-color:var(--background-primary); overflow-y:auto;">

        

{{-- Back link --}}
<div class="mb-4">
    <a href="{{ route('helpcenter.index') }}" 
       class="text-lg text-secondary font-semibold no-underline">
        &larr; Back to Helpcenter
    </a>
</div>

        {{-- Category title & description --}}
        <h1 style="font-size:1.875rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-primary);">
            {{ $category->name }}
        </h1>
        @if($category->description)
            <p style="font-size:1rem; margin-bottom:1.5rem; color:var(--text-secondary);">
                {{ $category->description }}
            </p>
        @endif

{{-- Articles Section --}}
@if($articles->count())
    <div style="display:flex; flex-direction:column; gap:0.5rem; margin-bottom:1rem;">
        @foreach($articles as $article)
            <a href="{{ route('helpcenter.show', $article) }}" 
               class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral rounded-lg"
               style="padding:0.5rem 0.75rem; text-decoration:none; color:inherit; box-shadow:0 2px 6px rgba(0,0,0,0.08); display:block;">
                <h3 style="font-weight:600; font-size:1rem; color:var(--text-primary); margin-bottom:0.25rem;">
                    {{ $article->title }}
                </h3>
                @if($article->description)
                    <p style="font-size:0.875rem; color:var(--text-secondary); margin:0;">
                        {{ $article->description }}
                    </p>
                @endif
            </a>
        @endforeach
    </div>
@else
    <p style="color:var(--text-primary);">
        {{ __('No articles found in this category.') }}
    </p>
@endif




    </div>
</div>
