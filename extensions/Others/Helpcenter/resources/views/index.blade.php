<div style="min-height:100vh; background-color:var(--background-primary); display:flex; flex-direction:column; padding:2rem;">

    {{-- Titel + subtitel --}}
    <div style="padding-top:0.5rem; padding-bottom:2rem; border-bottom:1px solid var(--border-color); text-align:left;">
        <h2 style="font-size:2rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-primary);">
            Helpcenter
        </h2>
        <div style="font-size:1rem; color:var(--text-secondary);">
            Find the answers to all your questions.
        </div>
    </div>

    {{-- Hoofdindeling --}}
    <div style="display:flex; flex-grow:1; margin-top:1rem; gap:2rem; flex-wrap:nowrap; align-items:flex-start;">

        {{-- Sidebar --}}
        <div style="width:250px; background-color:var(--background-secondary); padding:0 0; color:var(--text-primary); flex-shrink:0; border-right:1px solid var(--border-color);">
            <h3 style="font-size:1.125rem; font-weight:600; margin-bottom:1rem; color:var(--text-secondary); text-align:left; margin-top:0;">Links</h3>
            <nav>
                <ul style="list-style:none; padding-left:1.5rem; padding-right:1.5rem;">
                    @foreach($links as $link)   
                        <li style="margin-bottom:0.125rem;">
                            <a href="{{ $link->url }}"
                               style="display:flex; align-items:center; color:var(--text-secondary); text-decoration:none; padding:0.25rem 0.5rem; border-radius:0.375rem; transition: transform 0.3s ease;">
                               <span style="display:inline-block; transition: transform 0.3s ease;">
                                   {{ $link->title }}
                               </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        {{-- Main Content Area --}}
        <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:1.5rem;">
{{-- Search Bar Section --}}
<div class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral rounded-lg p-6 shadow-md w-full max-w-7xl mx-auto">
    <form action="{{ route('helpcenter.index') }}" method="GET" class="flex flex-row gap-4">
        <div class="flex-1 flex items-center px-4 py-3 rounded-md border border-neutral bg-background-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-text-secondary mr-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="How can we help you today?"
                   class="flex-1 bg-transparent border-none outline-none text-text-primary text-base sm:text-lg py-2 pl-2"/>
        </div>

        <button type="submit" class="search-btn bg-primary text-white border-none font-semibold cursor-pointer py-3 px-6 rounded-md text-base">
            Search
        </button>
    </form>
</div>



            {{-- Search Results Section --}}
            @if($search && $articles->count())
                <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-primary); text-align:left;">
                    Search Results
                </h2>
                <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2rem;">
                    @foreach($articles as $article)
                        <a href="{{ route('helpcenter.show', $article) }}" 
                           class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg transition-shadow"
                           style="text-decoration:none; color:inherit; display:block;">
                            <h3 style="font-weight:600; font-size:1.125rem; color:var(--text-primary); margin-bottom:0.5rem;">
                                {{ $article->title }}
                            </h3>
                            <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:0.5rem;">
                                {{ $article->description }}
                            </p>
                            <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; color:var(--text-secondary);">
                                <span class="bg-primary/10 text-primary px-2 py-1 rounded">{{ $article->category->name }}</span>
                                @if($article->published_at)
                                    <span>{{ $article->published_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            @if($search && !$articles->count() && !$categories->count() && !$globalFaqs->count())
                <div class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral rounded-lg p-6 text-center">
                    <p style="color:var(--text-secondary); font-size:1rem;">
                        No results found for "{{ $search }}". Try different keywords.
                    </p>
                </div>
            @endif

            {{-- Categories Section --}}
            @if($categories->count())
                <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-primary); text-align:left;">
                    Categories
                </h2>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:0.75rem;">
                    @foreach($categories as $cat)
                        <a href="{{ route('helpcenter.category', $cat) }}" 
                           class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg"
                           style="text-decoration:none; color:inherit; box-shadow:0 2px 6px rgba(0,0,0,0.08); display:flex; flex-direction:column; justify-content:space-between;">
                            <h3 style="font-weight:600; font-size:1rem; color:var(--text-primary); margin-bottom:0.5rem; display:flex; align-items:center;">
                                {{ $cat->name }} <span style="margin-left:auto; font-size:0.875rem; color:var(--text-secondary);">({{ $cat->articles_count ?? 0 }})</span>
                            </h3>
                            <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:0.5rem;">
                                {{ $cat->description ?? __('Browse articles in this category.') }}
                            </p>
                            <button class="bg-primary text-white border-none font-semibold cursor-pointer" 
                                    style="padding:0.35rem 0.5rem; border-radius:0.375rem; width:max-content; font-size:0.875rem;">
                                {{ __('View articles') }}
                            </button>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- FAQs Section --}}
            @if($globalFaqs->count())
                <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-primary); text-align:left;">
                    Frequently Asked Questions
                </h2>
                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    @foreach($globalFaqs as $faq)
                        <div x-data="{ open: false }"
                             class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral rounded-lg p-4">
                            <button @click="open = !open"
                                    class="flex justify-between items-center w-full text-left font-semibold text-base cursor-pointer">
                                <span>{{ $faq->question }}</span>
                                <svg :class="{'rotate-180': open}" class="w-5 h-5 transform transition-transform"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                 class="mt-3 text-sm text-muted leading-relaxed prose dark:prose-invert max-w-full">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>

<style>

li a span {
    display: inline-block;
    transition: transform 0.3s ease;
}
li a:hover span {
    transform: translateX(10px); 
}


@media (max-width: 768px) {
    
    div[style*="display:flex; flex-grow:1; margin-top:2rem;"] {
        flex-direction: column;
        gap:1rem;
    }

    
    div[style*="width:250px; background-color:var(--background-secondary)"] {
        display: none !important;
    }

   
    div[style*="flex:1; min-width:0; display:flex; flex-direction:column; gap:1.5rem;"] {
        width: 100% !important;
        align-items: stretch; 
    }

    
    div[style*="padding-top:2rem; padding-bottom:2rem; border-bottom:1px solid var(--border-color); text-align:left;"] {
        text-align:center !important;
    }

    
    div[style*="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr))"] {
        grid-template-columns:1fr !important;
    }
.search-btn {
    display: none !important;
}

        div[style*="flex:1; display:flex;"] input[type="text"] {
        padding: 1rem; /* van 0.75rem naar 1rem voor meer hoogte */
        font-size: 1rem; /* optioneel iets groter */
    }
}
</style>

</div>
