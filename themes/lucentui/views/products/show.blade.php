<div class="container mx-auto px-4 md:px-8 py-12">
    @php
        $pattern = '/!([a-zA-Z0-9_]+)=([^\r\n<]+)/';
        $rawDescription = $product->description ?? '';
        
        preg_match_all($pattern, $rawDescription, $matches, PREG_SET_ORDER);
        $specs = [];
        foreach ($matches as $match) {
            $specs[strtolower($match[1])] = trim($match[2]);
        }
    
        $cleanDescription = preg_replace($pattern, '', $rawDescription);
        $cleanDescription = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $cleanDescription);
    @endphp

    <div class="relative rounded-xl bg-background-secondary/40 backdrop-blur-xl border border-white/10 shadow-2xl p-6 lg:p-10 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-xl blur-3xl -z-10 translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="grid grid-cols-1 @if($product->image) lg:grid-cols-2 @endif gap-10 lg:gap-16 items-start">
            
            @if ($product->image)
                <div class="relative w-full aspect-[4/3] lg:aspect-square rounded-2xl overflow-hidden border border-white/10 shadow-lg group">
                    <img src="{{ Storage::url($product->image) }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-105">
                         
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
            @endif

            <div class="flex flex-col h-full">
                
                <div class="mb-4">
                    @if ($product->stock === 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400">
                            <x-ri-close-circle-fill class="size-4" />
                            {{ __('product.out_of_stock', ['product' => $product->name]) }}
                        </span>
                    @elseif($product->stock > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                            <x-ri-checkbox-circle-fill class="size-4" />
                            {{ __('product.in_stock') }}
                        </span>
                    @endif
                </div>

                <div class="mb-6">
                    <h1 class="text-3xl lg:text-4xl font-bold text-base leading-tight mb-3">
                        {{ $product->name }}
                    </h1>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-primary">
                            {{ $product->price()->formatted->price }}
                        </span>
                        @if($product->price()->billing_period > 0)
                            <span class="text-sm font-medium text-color-muted uppercase">
                                / {{ $product->price()->billing_period }} {{ $product->price()->billing_unit }}
                            </span>
                        @endif
                    </div>
                </div>

                @if (!empty(trim($cleanDescription)))
                    <div class="prose dark:prose-invert text-color-muted leading-relaxed max-w-none mb-2 pt-3 border-t border-neutral/30">
                        {!! $cleanDescription !!}
                    </div>
                @endif

                @if(!empty($specs))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mb-8">
                        @foreach($specs as $key => $val)
                            <div class="flex items-center gap-2.5">
                                <div class="shrink-0 text-primary">
                                     @switch(strtolower($key))
                                        @case('cpu') <x-ri-cpu-line class="size-5" /> @break
                                        @case('ram') <x-ri-microscope-line class="size-5" /> @break
                                        @case('disk') <x-ri-hard-drive-2-line class="size-5" /> @break
                                        @case('storage') <x-ri-hard-drive-2-line class="size-5" /> @break
                                        @case('port') <x-ri-global-line class="size-5" /> @break
                                        @case('bandwidth') <x-ri-speed-up-line class="size-5" /> @break
                                        @case('location') <x-ri-map-pin-line class="size-5" /> @break
                                        @case('backup') <x-ri-save-3-line class="size-5" /> @break
                                        @case('player') <x-ri-group-line class="size-5" /> @break
                                        @case('database') <x-ri-database-2-line class="size-5" /> @break
                                        @default <x-ri-checkbox-circle-line class="size-5" />
                                    @endswitch
                                </div>
                                <span class="font-medium text-sm text-color-base">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex-grow"></div>

                @if ($product->stock !== 0 && $product->price()->available)
                    <div class="mt-4 pt-6 border-t border-neutral/30">
                        <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}" wire:navigate class="block w-full sm:w-fit">
                            <x-button.primary class="w-full sm:w-auto px-8 py-3.5 text-base font-bold shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300">
                                <span class="flex items-center gap-2">
                                    {{ __('product.add_to_cart') }}
                                    <x-ri-arrow-right-line class="size-5" />
                                </span>
                            </x-button.primary>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>