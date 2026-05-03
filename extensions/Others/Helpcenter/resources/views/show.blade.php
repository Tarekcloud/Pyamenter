<div
    style="flex-grow:1; padding:2rem; background-color:var(--background-primary); overflow-y:auto;"
>

    {{-- Back link --}}
    <div class="mb-4">
        <a
            href="{{ route('helpcenter.category', $article->category->slug) }}"
            class="text-lg text-secondary font-semibold no-underline"
        >
            &larr; {{ __('Back to :category', ['category' => $article->category->name]) }}
        </a>
    </div>

    {{-- Breadcrumb subtitle --}}
    <div class="text-sm text-gray-500 mb-6">
        <span>
            <a href="{{ route('helpcenter.index') }}" class="hover:underline">
                {{ __('Helpcenter') }}
            </a>
        </span>
        <span class="mx-2">/</span>
        <span>
            <a
                href="{{ route('helpcenter.category', $article->category->slug) }}"
                class="hover:underline"
            >
                {{ $article->category->name }}
            </a>
        </span>
        <span class="mx-2">/</span>
        <span class="font-medium dark:text-gray-450 text-gray-700">
            {{ $article->title }}
        </span>
    </div>

    {{-- Article Card --}}
    <div
        class="{{ !empty($article->htmlcontent) 
            ? 'border border-neutral p-1 rounded-lg mb-8' 
            : 'bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-8 shadow-sm' }}"
    >
        @if(empty($article->htmlcontent))
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-text-primary m-0">
                    {{ $article->title }}
                </h1>
                @if($article->category)
                    <p class="text-sm text-text-secondary m-0">
                        {{ $article->category->name }}
                    </p>
                @endif
            </div>
        @endif

        <article class="{{ empty($article->htmlcontent) ? 'prose prose-green dark:prose-invert' : '' }}">
            @if(!empty($article->htmlcontent))
                {!! $article->htmlcontent !!}
            @elseif(!empty($article->rawcontent))
                {{-- Convert Markdown to HTML --}}
                {!! Str::markdown($article->rawcontent, [
                    'allow_unsafe_links' => false,
                    'renderer' => ['soft_break' => "<br>"]
                ]) !!}
            @else
                {!! $article->content !!}
            @endif
        </article>
    </div>

    {{-- Helpful Section --}}
    @php
        $voted = session("voted_article_{$article->id}", false);
        $totalVotes = $article->helpful_yes + $article->helpful_no;
    @endphp

    @if(!$voted)
        <div
            class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-8 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center space-y-2 md:space-y-0"
        >
            {{-- Left: Text --}}
            <div class="flex flex-col">
                <p class="text-base font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('Do you think this article is helpful?') }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0">
                    {{ $article->helpful_yes }} {{ __('people thought this article was helpful') }}
                    ({{ $totalVotes }} {{ __('votes') }})
                </p>
            </div>

            {{-- Right: Buttons --}}
            <div class="flex space-x-4">
                <form method="POST" action="{{ route('helpcenter.vote', [$article->slug, 'yes']) }}">
                    @csrf
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-lg border border-neutral text-gray-800 dark:text-gray-200 font-semibold transition"
                    >
                        {{ __('Yes') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('helpcenter.vote', [$article->slug, 'no']) }}">
                    @csrf
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-lg border border-neutral text-gray-800 dark:text-gray-200 font-semibold transition"
                    >
                        {{ __('No') }}
                    </button>
                </form>
            </div>
        </div>
    @else
        <div
            class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-8 shadow-sm"
        >
            {{ __('Thanks for your feedback!') }}
        </div>
    @endif

    {{-- FAQs --}}
    @if($faqs->count())
        <h2 class="text-xl font-bold mb-4 text-text-primary">
            {{ __('Frequently Asked Questions') }}
        </h2>
        <div class="flex flex-col gap-4">
            @foreach($faqs as $faq)
                <div
                    x-data="{ open: false }"
                    class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg shadow-sm"
                >
                    <button
                        @click="open = !open"
                        class="w-full flex justify-between items-center font-semibold text-text-primary text-base cursor-pointer bg-none border-none p-0"
                    >
                        <span>{{ $faq->question }}</span>
                        <svg
                            :class="{'rotate-180': open}"
                            class="w-5 h-5 transition-transform"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="mt-3 text-sm text-text-secondary leading-relaxed">
                        {!! $faq->answer !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
