<div class="container mx-auto px-4 py-8 animate-fade-in-up">
    
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-6">
        <div class="flex items-center gap-3">
            <div class="bg-primary/10 text-primary p-2 rounded-full shadow-md">
                <x-ri-article-fill class="w-5 h-5" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-color-base">{{ $title }}</h1>
                @if($description)
                    <p class="text-color-muted text-sm">{{ $description }}</p>
                @endif
            </div>
        </div>

        <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-ri-search-line class="w-4 h-4 text-neutral-500 group-focus-within:text-primary transition-colors" />
                </div>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="pl-9 pr-4 py-2 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 text-sm rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary w-full md:w-64 transition-all shadow-sm"
                    placeholder="{{ __('Search...') }}"
                />
            </div>

            <div class="relative">
                <select
                    wire:model.live="sortOption"
                    class="appearance-none pl-4 pr-9 py-2 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 text-sm rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary w-full cursor-pointer transition-all shadow-sm"
                >
                    <option value="published_at_desc">{{ __('Latest') }}</option>
                    <option value="published_at_asc">{{ __('Oldest') }}</option>
                    <option value="views_desc">{{ __('Most Viewed') }}</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-neutral-500">
                    <x-ri-sort-desc class="w-4 h-4" />
                </div>
            </div>
        </div>
    </div>

    @if($tagSuggestions->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 mb-8">
            <span class="text-[0.7rem] uppercase tracking-wide text-neutral-500 font-bold">{{ __('Topics') }}:</span>
            @foreach($tagSuggestions as $tag)
                <button
                    type="button"
                    wire:click='applyTag(@js($tag))'
                    class="px-3 py-1 text-xs font-medium rounded-lg border border-neutral/30 bg-background-secondary/20 backdrop-blur-md hover:bg-primary hover:text-white hover:border-primary transition-all duration-300"
                >
                    {{ $tag }}
                </button>
            @endforeach
            @if($search !== '')
                <button wire:click="clearSearch" class="ml-auto text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1">
                    <x-ri-close-circle-line class="w-3 h-3" /> {{ __('Clear filters') }}
                </button>
            @endif
        </div>
    @endif

    @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $index => $post)
                <a href="{{ route('blog.show', $post) }}" wire:navigate class="block h-full">
                    <div class="group h-full flex flex-col bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-2xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-[1.02] overflow-hidden animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                        
                        <div class="relative w-full aspect-video overflow-hidden border-b border-neutral/30">
                            @if($post->cover_image_url)
                                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-neutral/10 flex items-center justify-center">
                                    <x-ri-image-line class="text-2xl text-neutral-300" />
                                </div>
                            @endif
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
                            
                            <div class="absolute top-4 right-4 bg-background-secondary/80 backdrop-blur-md p-1.5 rounded-lg text-color-muted group-hover:text-primary transition-colors duration-300 shadow-sm">
                                <x-ri-arrow-right-up-fill class="w-3.5 h-3.5 transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-45" />
                            </div>
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex flex-wrap gap-2">
                                    @if($post->created_at->diffInDays() < 7)
                                        <div class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                                            <div class="size-1 bg-primary rounded-full animate-pulse"></div>
                                            <span class="text-[9px] font-bold uppercase tracking-wider">New</span>
                                        </div>
                                    @endif
                                    @foreach(collect($post->tags)->take(2) as $tag)
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-neutral/10 text-neutral-500 border border-neutral/20">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <h2 class="text-lg font-bold text-color-base mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-2 leading-tight">
                                {{ $post->title }}
                            </h2>

                            @if($post->description)
                                <p class="text-color-muted text-xs line-clamp-3 leading-relaxed mb-4">
                                    {{ $post->description }}
                                </p>
                            @endif

                            <div class="mt-auto pt-4 flex items-center justify-between border-t border-neutral/20">
                                <div class="flex items-center gap-1.5 text-color-muted text-[11px] font-medium">
                                    <x-ri-calendar-line class="w-3 h-3" />
                                    <span>{{ optional($post->published_at ?: $post->created_at)->format('M d, Y') }}</span>
                                </div>

                                <span class="group/btn inline-flex items-center gap-1.5 bg-primary hover:bg-primary/90 text-white px-3 py-1.5 rounded-lg font-medium text-[11px] transition-all duration-300 hover:shadow-lg hover:scale-105">
                                    {{ __('Read') }}
                                    <x-ri-arrow-right-fill class="w-3 h-3 transform transition-transform duration-300 group-hover/btn:translate-x-1" />
                                </span>
                            </div>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-primary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="border border-dashed border-neutral/50 rounded-2xl p-8 text-center bg-background-secondary/30">
            <div class="inline-flex items-center justify-center size-12 rounded-full bg-neutral/10 text-neutral-400 mb-3">
                <x-ri-file-search-line class="text-xl" />
            </div>
            <h3 class="text-base font-semibold text-color-base mb-1">{{ __('No posts found') }}</h3>
            <p class="text-neutral-500 text-xs">
                {{ $search !== '' ? __('Try adjusting your search or filters.') : __('Check back later for new updates.') }}
            </p>
            @if($search !== '')
                <button wire:click="clearSearch" class="mt-3 text-primary hover:underline text-xs font-medium">
                    {{ __('Clear all filters') }}
                </button>
            @endif
        </div>
    @endif
</div>