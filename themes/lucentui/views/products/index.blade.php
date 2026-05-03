<div class="container mx-auto px-8 py-8" x-data="{ 
    search: '', 
    viewMode: 'all',
    get filteredProducts() {
        const searchTerm = this.search.toLowerCase();
        const mode = this.viewMode;
        
        return Array.from(this.$refs.productsContainer?.children || [])
            .filter(child => child.classList.contains('product-item'))
            .filter(child => {
                if (mode === 'available' && child.dataset.available !== 'true') {
                    return false;
                }
                if (mode === 'unavailable' && child.dataset.available === 'true') {
                    return false;
                }
                
                if (this.search) {
                    const productName = child.dataset.productName?.toLowerCase() || '';
                    const productDescription = child.dataset.productDescription?.toLowerCase() || '';
                    return productName.includes(searchTerm) || productDescription.includes(searchTerm);
                }
                
                return true;
            }).length > 0;
    },
    get visibleProductsCount() {
        const searchTerm = this.search.toLowerCase();
        const mode = this.viewMode;
        
        return Array.from(this.$refs.productsContainer?.children || [])
            .filter(child => child.classList.contains('product-item'))
            .filter(child => {
                if (mode === 'available' && child.dataset.available !== 'true') {
                    return false;
                }
                if (mode === 'unavailable' && child.dataset.available === 'true') {
                    return false;
                }
                
                if (this.search) {
                    const productName = child.dataset.productName?.toLowerCase() || '';
                    const productDescription = child.dataset.productDescription?.toLowerCase() || '';
                    return productName.includes(searchTerm) || productDescription.includes(searchTerm);
                }
                
                return true;
            }).length;
        }
    }">

    @if ($category->image && theme('show_category_image_banner', true))
        <div class="mb-8 rounded-xl overflow-hidden shadow-xl">
            <div class="relative h-48 md:h-60 lg:h-72">
                <img src="{{ Storage::url($category->image) }}" 
                     alt="{{ $category->name }}"
                     class="w-full h-full object-cover object-center">
                
                <div class="absolute inset-0 bg-gradient-to-t from-primary/30 via-primary/15 to-primary/10"></div>
                
                <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-primary/30 backdrop-blur-sm text-primary p-2.5 rounded-xl shadow-lg">
                            <x-ri-folder-fill class="size-5" />
                        </div>
                    </div>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white text-shadow-lg mb-2 drop-shadow-2xl">
                        {{ $category->name }}
                    </h1>
                    @if ($category->description)
                        <div class="hidden md:block text-white md:text-lg text-shadow-lg max-w-3xl drop-shadow-lg ">
                            {!! Str::limit($category->description, 215, '...') !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-4 gap-8 lg:gap-12">
        
        <div class="flex flex-col gap-6 col-span-3 md:col-span-1">
            
            @if (!$category->image || !theme('show_category_image_banner', true))
                <div class="group bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-primary/10 text-primary p-3 rounded-xl shadow-md">
                                <x-ri-folder-fill class="size-6" />
                            </div>
                        </div>
                        
                        <h1 class="text-3xl font-bold text-base mb-3 group-hover:text-primary transition-colors duration-300">
                            {{ $category->name }}
                        </h1>
                        
                        @if ($category->description)
                            <article class="prose dark:prose-invert text-color-muted leading-relaxed">
                                {!! $category->description !!}
                            </article>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 pb-4 border-b border-neutral/50">
                    <div class="flex items-center justify-center">
                        <div class="bg-primary/10 text-primary p-3 rounded-xl">
                            <x-ri-list-check-2 class="size-8" />
                        </div>
                    </div>
                </div>
                
                <nav class="flex flex-col py-2">
                    @foreach ($categories as $ccategory)
                        <a href="{{ route('category.show', ['category' => $ccategory->slug]) }}" wire:navigate
                           class="group/nav px-6 py-3 text-base hover:bg-primary/10 hover:text-primary transition-all duration-300 border-l-4 border-transparent hover:border-primary {{ $category->id == $ccategory->id ? 'font-bold text-primary bg-primary/10 border-primary' : '' }}"
                           aria-current="{{ $category->id == $ccategory->id ? 'page' : 'false' }}">
                           <div class="flex items-center justify-between">
                               <span>{{ $ccategory->name }}</span>
                               <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-300 group-hover/nav:translate-x-1 opacity-0 group-hover/nav:opacity-100" />
                           </div>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="flex flex-col gap-8 col-span-3">
            
            @if (count($childCategories) >= 1)
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="hidden md:flex items-center gap-2 text-color-muted">
                            <x-ri-stack-fill class="size-4" />
                            <span class="text-sm">{{ count($childCategories) }} {{ Str::plural('category', count($childCategories)) }}</span>
                        </div>
                    </div>
                    
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($childCategories as $index => $childCategory)
                            @php
                                $hasImage = !empty($childCategory->image);
                            @endphp

                            <div class="group relative bg-gradient-to-br from-background-secondary via-background-secondary/90 to-background-secondary/70 border border-neutral/50 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-3 transition-all duration-500 overflow-hidden flex flex-col" 
                                 style="animation-delay: {{ $index * 0.1 }}s;">
                                
                                @if(theme('small_images', false))
                                    <div class="p-6 flex flex-col flex-1">
                                        <div class="flex gap-x-3 items-center mb-4">
                                            @if ($hasImage)
                                                <img src="{{ Storage::url($childCategory->image) }}" alt="{{ $childCategory->name }}"
                                                    class="w-14 h-14 object-cover rounded-xl shadow-md">
                                            @endif
                                            <h3 class="text-xl font-bold text-base group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                                {{ $childCategory->name }}
                                            </h3>
                                        </div>
                                        
                                        @if(theme('show_category_description', true) && $childCategory->description)
                                            <article class="prose dark:prose-invert text-color-muted text-sm mb-4 leading-relaxed">
                                                {!! $childCategory->description !!}
                                            </article>
                                        @endif
                                        
                                        <a href="{{ route('category.show', ['category' => $childCategory->slug]) }}" wire:navigate 
                                           class="group/btn w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105">
                                            {{ __('common.button.view') }}
                                            <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-300 group-hover/btn:translate-x-1" />
                                        </a>
                                    </div>
                                @else
                                    @if ($hasImage)
                                        <div class="relative overflow-hidden shrink-0">
                                            <img src="{{ Storage::url($childCategory->image) }}" alt="{{ $childCategory->name }}"
                                                class="w-full h-56 object-cover">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                        </div>
                                    @endif
                                    
                                    <div class="p-6 flex flex-col flex-1">
                                        <h3 class="text-xl font-bold text-base mb-4 group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                            {{ $childCategory->name }}
                                        </h3>
                                        
                                        @if(theme('show_category_description', true) && $childCategory->description)
                                            <article class="prose dark:prose-invert text-color-muted text-sm mb-4 leading-relaxed">
                                                {!! $childCategory->description !!}
                                            </article>
                                        @endif
                                        
                                        <div class="mt-auto">
                                            <a href="{{ route('category.show', ['category' => $childCategory->slug]) }}" wire:navigate 
                                               class="group/btn w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105">
                                                {{ __('common.button.view') }}
                                                <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-300 group-hover/btn:translate-x-1" />
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-primary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 transition-colors duration-200" 
                                 :class="search ? 'text-primary' : 'text-gray-400'"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input
                            x-model.debounce.300ms="search"
                            type="text"
                            placeholder="{{ __('Search products...') }}"
                            class="w-full pl-11 pr-4 py-3 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 hover:bg-background-secondary/60 focus:bg-background-secondary/70"
                        >
                        <button x-show="search" 
                                x-cloak
                                @click="search = ''"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-primary transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="search || viewMode !== 'all'" 
                         x-cloak 
                         class="mt-2 text-sm text-color-muted transition-all duration-300 transform"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <span x-show="search && viewMode !== 'all'">
                            Showing <span class="font-bold text-primary" x-text="visibleProductsCount"></span> results for "<strong class="text-primary" x-text="search"></strong>" in <strong class="text-primary capitalize" x-text="viewMode"></strong> products
                        </span>
                        <span x-show="search && viewMode === 'all'">
                            Showing <span class="font-bold text-primary" x-text="visibleProductsCount"></span> results for "<strong class="text-primary" x-text="search"></strong>"
                        </span>
                        <span x-show="!search && viewMode !== 'all'">
                            Showing <span class="font-bold text-primary" x-text="visibleProductsCount"></span> <strong class="text-primary capitalize" x-text="viewMode"></strong> products
                        </span>
                    </div>
                </div>

                <div class="mb-8 overflow-x-auto pb-2 scrollbar-hide">
                    <div class="flex flex-col sm:flex-row sm:inline-flex p-1 bg-background-secondary/40 border border-neutral/50 rounded-xl backdrop-blur-md gap-1 sm:gap-0">
                        
                        <button @click="viewMode = 'all'" 
                                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                                :class="viewMode === 'all' ? 'bg-background shadow-sm text-primary font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                            <x-ri-apps-line class="size-4" x-bind:class="viewMode === 'all' ? 'text-primary' : 'opacity-70'" />
                            All Products
                            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10" 
                                :class="viewMode === 'all' ? 'bg-primary/10 text-primary' : 'text-color-muted'">
                                {{ $products->count() }}
                            </span>
                        </button>

                        <div class="w-px bg-neutral/30 my-2 mx-1"></div>

                        <button @click="viewMode = 'available'" 
                                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                                :class="viewMode === 'available' ? 'bg-background shadow-sm text-success font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                            <x-ri-checkbox-circle-line class="size-4" x-bind:class="viewMode === 'available' ? 'text-success' : 'opacity-70'" />
                            {{ __('product.in_stock') }}
                            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                                :class="viewMode === 'available' ? 'bg-success/10 text-success' : 'text-color-muted'">
                                {{ $products->filter(fn($p) => $p->stock !== 0 && $p->price()->available)->count() }}
                            </span>
                        </button>

                        <button @click="viewMode = 'unavailable'" 
                                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                                :class="viewMode === 'unavailable' ? 'bg-background shadow-sm text-error font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                            <x-ri-close-circle-line class="size-4" x-bind:class="viewMode === 'unavailable' ? 'text-error' : 'opacity-70'" />
                            Out of Stock
                            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                                :class="viewMode === 'unavailable' ? 'bg-error/10 text-error' : 'text-color-muted'">
                                {{ $products->filter(fn($p) => $p->stock === 0 || !$p->price()->available)->count() }}
                            </span>
                        </button>

                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="hidden md:flex items-center gap-2 text-color-muted">
                        <x-ri-price-tag-3-fill class="size-4" />
                        <span class="text-sm">{{ count($products) }} {{ Str::plural('product', count($products)) }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-ref="productsContainer">
                    @foreach ($products as $index => $product)
                        @php
                            $isAvailable = ($product->stock !== 0) && $product->price()->available;
                            $pattern = '/!([a-zA-Z0-9_]+)=([^\r\n<]+)/';
                            $rawDescription = $product->description ?? '';
                            
                            preg_match_all($pattern, $rawDescription, $matches, PREG_SET_ORDER);
                            $specs = [];
                            foreach ($matches as $match) {
                                $specs[strtolower($match[1])] = trim($match[2]);
                            }
                            
                            $cleanDescription = preg_replace($pattern, '', $rawDescription);
                            $cleanDescription = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $cleanDescription);
                            
                            $productDescription = strip_tags($cleanDescription ?? '');
                        @endphp

                        <div class="product-item group relative bg-gradient-to-br from-background-secondary via-background-secondary/90 to-background-secondary/70 border border-neutral/50 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-3 transition-all duration-500 overflow-hidden flex flex-col" 
                             style="animation-delay: {{ $index * 0.1 }}s;"
                             data-product-name="{{ strtolower($product->name) }}"
                             data-product-description="{{ strtolower($productDescription) }}"
                             data-available="{{ $isAvailable ? 'true' : 'false' }}"
                             x-show="(viewMode === 'all' || (viewMode === 'available' && {{ $isAvailable ? 'true' : 'false' }}) || (viewMode === 'unavailable' && {{ !$isAvailable ? 'true' : 'false' }})) && (search === '' || '{{ strtolower($product->name . ' ' . $productDescription) }}'.includes(search.toLowerCase()))"
                             x-transition:enter="transition ease-out duration-500 delay-75"
                             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
                             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2">
                            
                            <div class="absolute top-4 right-4 z-10">
                                <div class="flex items-center gap-2 bg-background-secondary/70 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg">
                                    <div class="w-2 h-2 {{ $isAvailable ? 'bg-green-500' : 'bg-red-500' }} rounded-xl"></div>
                                    <span class="text-xs font-bold {{ $isAvailable ? 'text-green-600' : 'text-red-600' }} uppercase">
                                        {{ $isAvailable ? 'Available' : 'Out of Stock' }}
                                    </span>
                                </div>
                            </div>

                            @if(theme('small_images', false))
                                <div class="p-6 pt-12 flex flex-col flex-1">
                                    <div class="flex items-start gap-4 mb-4">
                                        @if ($product->image)
                                            <div class="flex-shrink-0 relative">
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                                    class="w-20 h-20 object-cover rounded-xl shadow-lg {{ !$isAvailable ? 'filter grayscale opacity-60' : '' }}">
                                                @if (!$isAvailable)
                                                    <div class="absolute inset-0 bg-red-500/20 rounded-xl"></div>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-base mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                                {{ $product->name }}
                                            </h3>
                                            <p class="text-2xl font-bold {{ $isAvailable ? 'text-primary' : 'text-color-muted line-through' }} mb-2">
                                                {{ $product->price()->formatted->price }}
                                            </p>
                                            @if($product->stock !== null)
                                                <div class="text-xs text-color-muted">
                                                    {{ $product->stock }} in stock
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(theme('direct_checkout', false) && !empty(trim($cleanDescription)))
                                        <article class="prose dark:prose-invert text-color-muted text-sm mb-2 leading-relaxed">
                                            {!! $cleanDescription !!}
                                        </article>
                                    @endif

                                    @if(!empty($specs))
                                    <div class="flex flex-col gap-2 mb-6 mt-2">
                                        @foreach($specs as $key => $val)
                                            <div class="flex items-center gap-3 text-sm text-color-muted">
                                                <div class="shrink-0 text-primary">
                                                     @switch(strtolower($key))
                                                        @case('cpu') <x-ri-cpu-line class="size-5" /> @break
                                                        @case('ram') <x-ri-database-2-line class="size-5" /> @break
                                                        @case('disk') <x-ri-hard-drive-2-line class="size-5" /> @break
                                                        @case('storage') <x-ri-hard-drive-2-line class="size-5" /> @break
                                                        @case('port') <x-ri-global-line class="size-5" /> @break
                                                        @case('bandwidth') <x-ri-speed-up-line class="size-5" /> @break
                                                        @case('location') <x-ri-map-pin-line class="size-5" /> @break
                                                        @case('backup') <x-ri-save-3-line class="size-5" /> @break
                                                        @case('database') <x-ri-database-2-line class="size-5" /> @break
                                                        @case('player') <x-ri-team-line class="size-5" /> @break
                                                        @default <x-ri-checkbox-circle-line class="size-5" />
                                                    @endswitch
                                                </div>
                                                <span class="font-medium text-color-base truncate">{{ $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    <div class="mt-auto pt-2 flex items-center gap-2">
                                        @if ($isAvailable && theme('direct_checkout', false))
                                            <a href="{{ route('products.checkout', ['category' => $product->category, 'product' => $product->slug]) }}" wire:navigate 
                                               class="group/btn flex-grow inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105">
                                                {{ __('product.add_to_cart') }}
                                                <x-ri-shopping-cart-fill class="size-4 transform transition-transform duration-300 group-hover/btn:scale-110" />
                                            </a>
                                        @elseif ($isAvailable)
                                            <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}" wire:navigate 
                                               class="group/btn flex-grow inline-flex items-center justify-center gap-2 bg-background-tertiary hover:bg-background-tertiary/80 border border-neutral/50 text-base px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg">
                                                {{ __('common.button.view') }}
                                                <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-300 group-hover/btn:translate-x-1" />
                                            </a>
                                        @else
                                            <button disabled 
                                                    class="flex-grow inline-flex items-center justify-center gap-2 bg-red-100 dark:bg-red-900/30 border border-red-500 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl font-medium cursor-not-allowed opacity-75">
                                                {{ __('product.out_of_stock', ['product' => $product->name]) }}
                                                <x-ri-close-circle-fill class="size-4" />
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @if ($product->image)
                                    <div class="relative overflow-hidden shrink-0">
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                            class="w-full h-56 object-cover {{ !$isAvailable ? 'filter grayscale opacity-60' : '' }}">
                                        @if (!$isAvailable)
                                            <div class="absolute inset-0 bg-red-500/20"></div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    </div>
                                @endif
                                
                                <div class="p-6 flex flex-col flex-1">
                                    <h3 class="text-xl font-bold text-base mb-4 group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                        {{ $product->name }}
                                    </h3>
                                    
                                    @if(theme('direct_checkout', false) && !empty(trim($cleanDescription)))
                                        <article class="prose dark:prose-invert text-color-muted text-sm mb-2 leading-relaxed">
                                            {!! $cleanDescription !!}
                                        </article>
                                    @endif

                                    @if(!empty($specs))
                                    <div class="flex flex-col gap-2 mb-6 mt-2">
                                        @foreach($specs as $key => $val)
                                            <div class="flex items-center gap-3 text-sm text-color-muted">
                                                <div class="shrink-0 text-primary">
                                                     @switch(strtolower($key))
                                                        @case('cpu') <x-ri-cpu-line class="size-5" /> @break
                                                        @case('ram') <x-ri-database-2-line class="size-5" /> @break
                                                        @case('disk') <x-ri-hard-drive-2-line class="size-5" /> @break
                                                        @case('storage') <x-ri-hard-drive-2-line class="size-5" /> @break
                                                        @case('port') <x-ri-global-line class="size-5" /> @break
                                                        @case('bandwidth') <x-ri-speed-up-line class="size-5" /> @break
                                                        @case('location') <x-ri-map-pin-line class="size-5" /> @break
                                                        @case('backup') <x-ri-save-3-line class="size-5" /> @break
                                                        @case('database') <x-ri-database-2-line class="size-5" /> @break
                                                        @case('player') <x-ri-team-line class="size-5" /> @break
                                                        @default <x-ri-checkbox-circle-line class="size-5" />
                                                    @endswitch
                                                </div>
                                                <span class="font-medium text-color-base truncate">{{ $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    <div class="mt-auto pt-4 border-t border-neutral/30">
                                        <div class="flex items-center justify-between mb-4">
                                            <p class="text-2xl font-bold {{ $isAvailable ? 'text-primary' : 'text-color-muted line-through' }}">
                                                {{ $product->price()->formatted->price }}
                                            </p>
                                            @if($product->stock !== null)
                                                <div class="text-xs text-color-muted font-medium">
                                                    {{ $product->stock }} in stock
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            @if ($isAvailable && theme('direct_checkout', false))
                                                <a href="{{ route('products.checkout', ['category' => $product->category, 'product' => $product->slug]) }}" wire:navigate 
                                                   class="group/btn flex-grow inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105">
                                                    {{ __('product.add_to_cart') }}
                                                    <x-ri-shopping-cart-fill class="size-4 transform transition-transform duration-300 group-hover/btn:scale-110" />
                                                </a>
                                            @elseif ($isAvailable)
                                                <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}" wire:navigate 
                                                   class="group/btn flex-grow inline-flex items-center justify-center gap-2 bg-background-tertiary hover:bg-background-tertiary/80 border border-neutral/50 text-base px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg">
                                                    {{ __('common.button.view') }}
                                                    <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-300 group-hover/btn:translate-x-1" />
                                                </a>
                                            @else
                                                <button disabled 
                                                        class="flex-grow inline-flex items-center justify-center gap-2 bg-red-100 dark:bg-red-900/30 border border-red-500 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl font-medium cursor-not-allowed opacity-75">
                                                    {{ __('product.out_of_stock', ['product' => $product->name]) }}
                                                    <x-ri-close-circle-fill class="size-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-br {{ $isAvailable ? 'from-primary/5 to-primary/10' : 'from-red-500/5 to-red-500/10' }} opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>
                    @endforeach

                    @if($products->isNotEmpty())
                    <div x-show="(search !== '' || viewMode !== 'all') && !filteredProducts"
                         x-cloak
                         class="col-span-full bg-background-secondary/20 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50 text-center"
                         x-transition:enter="transition ease-out duration-500 delay-200"
                         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
                         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2">
                        <x-ri-search-2-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
                        <h3 class="text-xl font-bold text-base mb-2">{{ __('No products found') }}</h3>
                        
                        <div x-show="search && viewMode !== 'all'">
                            <p class="text-color-muted mb-4">
                                No <strong class="text-primary capitalize" x-text="viewMode"></strong> products match "<strong class="text-primary" x-text="search"></strong>"
                            </p>
                        </div>
                        <div x-show="search && viewMode === 'all'">
                            <p class="text-color-muted mb-4">
                                Your search for "<strong class="text-primary" x-text="search"></strong>" did not match any products.
                            </p>
                        </div>
                        <div x-show="!search && viewMode !== 'all'">
                            <p class="text-color-muted mb-4">
                                No <strong class="text-primary capitalize" x-text="viewMode"></strong> products found.
                            </p>
                        </div>
                        
                        <div class="flex gap-2 justify-center flex-wrap">
                            <button x-show="search" 
                                    @click="search = ''" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary rounded-xl transition-all duration-300 hover:scale-105">
                                <x-ri-close-line class="size-4" />
                                Clear Search
                            </button>
                            <button x-show="viewMode !== 'all'" 
                                    @click="viewMode = 'all'" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-secondary/10 hover:bg-secondary/20 text-secondary rounded-xl transition-all duration-300 hover:scale-105">
                                <x-ri-filter-off-line class="size-4" />
                                Show All
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                @if($products->isEmpty())
                <div class="bg-background-secondary/20 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50 text-center">
                    <x-ri-inbox-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
                    <h3 class="text-xl font-bold text-base mb-2">No products yet.</h3>
                    <p class="text-color-muted">This category doesn't have any products at the moment.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>