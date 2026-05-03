<div class="container mx-auto px-4 sm:px-8 py-8 mt-12">
    
    <div class="mb-6">
        <a href="{{ route('blog.index') }}" wire:navigate
           class="inline-flex items-center text-primary hover:text-primary-dark transition-colors duration-200 group text-sm">
            <x-ri-arrow-left-line class="w-4 h-4 mr-1.5 transform transition-transform duration-300 group-hover:-translate-x-1" />
            <span class="font-medium">{{ __('Back to Blog') }}</span>
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        @if(!empty($tableOfContents))
            <aside class="hidden lg:block lg:w-72 lg:flex-shrink-0">
                <div class="sticky top-24 rounded-2xl border border-neutral/30 bg-background-secondary/50 p-5 shadow-sm backdrop-blur-sm">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-4 flex items-center gap-2">
                        <x-ri-list-check class="w-3.5 h-3.5" /> {{ __('Table of Contents') }}
                    </h2>
                    <nav class="space-y-1 text-sm border-l-2 border-neutral/20 ml-1">
                        @foreach($tableOfContents as $heading)
                            <a
                                href="#{{ $heading['id'] }}"
                                data-blog-toc-link
                                data-target="{{ $heading['id'] }}"
                                class="block pl-4 py-2 text-neutral-500 transition-all duration-200 hover:text-primary hover:border-l-2 hover:border-primary -ml-[2px]"
                                style="padding-left: {{ ($heading['level'] - 1) * 1 }}rem;"
                            >
                                <span class="leading-snug line-clamp-1">{{ $heading['text'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>
        @endif

        <article class="flex-1 bg-background-secondary border border-neutral rounded-2xl shadow-xl overflow-hidden">
            
            @if($post->cover_image_url)
                <div class="relative w-full h-64 sm:h-80 lg:h-96 bg-neutral/10">
                    <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-background-secondary to-transparent"></div>
                </div>
            @endif

            <div class="relative p-6 sm:p-8 lg:p-10 pb-0">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-primary/10 opacity-70 z-0 pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="flex-1 space-y-4">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-color-base leading-tight">
                            {{ $post->title }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-4 text-color-muted text-sm sm:text-base">
                            <div class="flex items-center gap-1.5 text-xs sm:text-sm font-medium">
                                <x-ri-calendar-line class="w-4 h-4 text-primary" />
                                <span>{{ optional($post->published_at ?: $post->created_at)->format('F d, Y') }}</span>
                            </div>
                            <div class="hidden sm:block text-neutral/30">•</div>
                            <div class="flex items-center gap-1.5 text-xs sm:text-sm font-medium">
                                <x-ri-eye-line class="w-4 h-4 text-primary" />
                                <span>{{ number_format($post->views ?? 0) }} {{ __('views') }}</span>
                            </div>
                        </div>
                    </div>

                    @if(!empty($post->tags))
                        <div class="flex-shrink-0 flex flex-wrap justify-end gap-2 max-w-[200px]">
                            @foreach($post->tags as $tag)
                                <span class="bg-primary/10 text-primary border border-primary/20 px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold uppercase tracking-wide shadow-sm">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <hr class="mt-8 border-neutral/20 relative z-10">
            </div>

            <div class="p-6 sm:p-8 lg:p-10 pt-8 prose dark:prose-invert max-w-none text-color-base leading-relaxed prose-headings:font-bold prose-headings:text-color-base prose-a:text-primary hover:prose-a:text-primary-dark prose-img:rounded-xl prose-img:shadow-lg">
                @if($post->description)
                    <p class="lead text-lg sm:text-xl text-neutral-500 font-medium mb-8 not-prose border-l-4 border-primary/50 pl-4 italic">
                        {{ $post->description }}
                    </p>
                @endif
                
                {!! $contentHtml !!}
            </div>
        </article>
    </div>
</div>

@once
    <script>
        (function () {
            function registerBlogTocLinks() {
                document.querySelectorAll('[data-blog-toc-link]').forEach(function (link) {
                    if (link.dataset.tocBound) return;
                    link.dataset.tocBound = 'true';
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        var targetId = link.dataset.target;
                        if (!targetId) return;
                        var target = document.getElementById(targetId);
                        if (!target) return;
                        var offset = 100;
                        var top = target.getBoundingClientRect().top + window.scrollY - offset;
                        window.scrollTo({ top: top, behavior: 'smooth' });
                    });
                });
            }
            document.addEventListener('DOMContentLoaded', registerBlogTocLinks);
            document.addEventListener('livewire:navigated', registerBlogTocLinks);
        })();
    </script>
@endonce