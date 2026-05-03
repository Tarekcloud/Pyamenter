@php

// Simple icon mapping logic, can be improved later tho (version 3?)
    $publicIconAssetPath = 'assets/lucentui/icons'; 
    $jsonPath = base_path('themes/lucentui/config.json');
    $iconData = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : ['ids' => [], 'keywords' => []];

    $getIconUrl = function($id, $name) use ($iconData, $publicIconAssetPath) {
        $foundIcon = $iconData['ids'][$id] ?? null;
        if (!$foundIcon) {
            $lowerName = strtolower($name);
            foreach ($iconData['keywords'] ?? [] as $key => $filename) {
                if (str_contains($lowerName, $key)) {
                    $foundIcon = $filename;
                    break;
                }
            }
        }
        return $foundIcon ? (str_starts_with($foundIcon, 'http') ? $foundIcon : asset($publicIconAssetPath . '/' . $foundIcon)) : null;
    };

    // Logic Parsing Specs
    $pattern = '/!([a-zA-Z0-9_]+)=([^\r\n<]+)/';
    $rawDescription = $product->description ?? '';
    
    // Ekstrak specs ke array
    preg_match_all($pattern, $rawDescription, $matches, PREG_SET_ORDER);
    $specs = [];
    foreach ($matches as $match) {
        $specs[strtolower($match[1])] = trim($match[2]);
    }
    
    // Bersihkan deskripsi dari tag !key=value untuk ditampilkan
    $cleanDescription = preg_replace($pattern, '', $rawDescription);
    $cleanDescription = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $cleanDescription);
@endphp

<div class="container mx-auto px-4 md:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">  
        
        <div class="lg:col-span-8 flex flex-col gap-10">
            
            <div class="rounded-2xl bg-background-secondary/30 backdrop-blur-md border border-white/10 p-6 md:p-8">
                <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-start md:items-center">
                    @if ($product->image)
                        <div class="flex-shrink-0 w-24 h-24 md:w-32 md:h-32 rounded-xl bg-background/50 backdrop-blur-sm overflow-hidden border border-white/10 shadow-sm">
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    
                    <div class="flex-grow">
                        <h1 class="text-3xl font-bold text-base tracking-tight mb-2">
                            {{ $product->name }}
                        </h1>
                        
                        @if (!empty(trim($cleanDescription)))
                            <div class="prose prose-sm dark:prose-invert text-color-muted leading-relaxed max-w-2xl">
                                {!! $cleanDescription !!}
                            </div>
                        @endif

                        @if(!empty($specs))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mt-2 pt-2 border-t border-white/10">
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
                                                @case('players') <x-ri-group-line class="size-5" /> @break
                                                @case('database') <x-ri-database-2-line class="size-5" /> @break
                                                @default <x-ri-checkbox-circle-line class="size-5" />
                                            @endswitch
                                        </div>
                                        <span class="font-medium text-sm text-color-base">{{ $val }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($product->availablePlans()->count() > 1)
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($product->availablePlans() as $availablePlan)
                            @php
                                $planIconUrl = $getIconUrl('plan_' . $availablePlan->billing_unit, $availablePlan->billing_unit);
                            @endphp

                            <label class="relative cursor-pointer group">
                                <input type="radio" value="{{ $availablePlan->id }}" wire:model.live="plan_id" class="peer sr-only" />
                                
                                <div class="relative h-full p-5 rounded-xl border border-white/10 bg-background-secondary/40 backdrop-blur-md transition-all duration-300
                                    hover:bg-background-secondary/60 hover:border-white/20 hover:shadow-lg
                                    peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:shadow-[0_0_15px_rgba(var(--primary-rgb),0.1)]">
                                    
                                    <div class="flex flex-col h-full justify-between gap-4">
                                        <div class="flex justify-between items-start">
                                            <div class="text-primary/80 group-hover:text-primary transition-colors duration-300">
                                                @if($planIconUrl)
                                                    <img src="{{ $planIconUrl }}" class="w-6 h-6 object-contain drop-shadow-md" />
                                                @else
                                                    <x-ri-time-line class="size-6" />
                                                @endif
                                            </div>

                                            <x-ri-checkbox-circle-fill class="size-5 text-primary opacity-0 peer-checked:opacity-100 transition-opacity absolute top-4 right-4" />
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-base mb-1 group-hover:text-primary transition-colors">
                                                {{ $availablePlan->name }}
                                            </h3>
                                            
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-bold text-base">
                                                    {{ $availablePlan->price()->formatted->price }}
                                                </span>
                                                <span class="text-xs text-color-muted font-medium">
                                                    / {{ $availablePlan->billing_period }} {{ trans_choice(__('services.billing_cycles.' . $availablePlan->billing_unit), $availablePlan->billing_period) }}
                                                </span>
                                            </div>

                                            @if ($availablePlan->price()->has_setup_fee)
                                                <p class="text-xs text-color-muted mt-1 bg-white/5 w-fit px-2 py-0.5 rounded-md border border-white/5">
                                                    +{{ $availablePlan->price()->formatted->setup_fee }} Setup
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($product->configOptions->count() > 0 || count($this->getCheckoutConfig()) > 0)
                <div class="space-y-6">
                    <div class="space-y-8 rounded-2xl bg-background-secondary/20 backdrop-blur-sm border border-white/5 p-6">
                        @foreach ($product->configOptions as $configOption)
                            @php
                                $showPriceTag = $configOption->children->filter(fn ($value) => !$value->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->is_free)->count() > 0;
                            @endphp
                            
                            <div class="space-y-3">
                                @if ($configOption->type == 'radio')
                                    <label class="text-xl font-bold text-base flex items-center gap-1">
                                        {{ $configOption->name }}
                                        @if ($configOption->required) <span class="text-red-500">*</span> @endif
                                    </label>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                            @foreach ($configOption->children as $child)
                                                @php
                                                    $iconUrl = $getIconUrl($child->id, $child->name);
                                                @endphp

                                                <label class="relative cursor-pointer group">
                                                    <input type="radio" value="{{ $child->id }}" wire:model.live="configOptions.{{ $configOption->id }}" class="peer sr-only" />
                                                    
                                                    <div class="flex flex-col items-center justify-center p-3 rounded-xl border border-white/10 bg-background-secondary/40 backdrop-blur-sm h-full text-center transition-all duration-200
                                                        hover:bg-background-secondary/60 hover:border-white/20
                                                        peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:text-primary">
                                                        
                                                        @if($iconUrl)
                                                            <img src="{{ $iconUrl }}" class="w-8 h-8 object-contain mb-4 opacity-80 group-hover:opacity-100 drop-shadow-sm" />
                                                        @endif

                                                        <span class="text-sm font-medium leading-tight">
                                                            {{ $child->name }}
                                                        </span>

                                                        @if ($showPriceTag)
                                                            <span class="mt-1 text-[10px] text-color-muted bg-background/30 px-1.5 py-0.5 rounded border border-white/5">
                                                                {{ $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available ? $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : 'Free' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                    </div>
                                @else
                                    <x-form.configoption :config="$configOption" :name="'configOptions.' . $configOption->id" class="w-full bg-background-secondary/40 backdrop-blur-sm border-white/10 focus:border-primary focus:ring-primary rounded-xl px-4 py-2.5" :showPriceTag="$showPriceTag" :plan="$plan">
                                            @if ($configOption->type == 'select')
                                                @foreach ($configOption->children as $configOptionValue)
                                                    <option value="{{ $configOptionValue->id }}">
                                                        {{ $configOptionValue->name }}
                                                        {{ ($showPriceTag && $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' - ' . $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                    </x-form.configoption>
                                @endif
                            </div>
                        @endforeach

                        @foreach ($this->getCheckoutConfig() as $configOption)
                            @php $configOption = (object) $configOption; @endphp
                            <div class="space-y-3">
                                <label class="text-sm font-medium text-base flex items-center gap-1">
                                    {{ $configOption->label ?? ucfirst(str_replace('_', ' ', $configOption->name)) }}
                                    @if ($configOption->required ?? false) <span class="text-red-500">*</span> @endif
                                </label>

                                @if ($configOption->type == 'radio')
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                            @foreach ($configOption->options as $optionValue => $optionLabel)
                                                @php $iconUrl = $getIconUrl($optionValue, $optionLabel); @endphp
                                                <label class="relative cursor-pointer group">
                                                    <input type="radio" value="{{ $optionValue }}" wire:model.live="checkoutConfig.{{ $configOption->name }}" class="peer sr-only" />
                                                    <div class="flex flex-col items-center justify-center p-3 rounded-xl border border-white/10 bg-background-secondary/40 backdrop-blur-sm h-full text-center transition-all duration-200
                                                        hover:bg-background-secondary/60 hover:border-white/20
                                                        peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:text-primary">
                                                        @if($iconUrl)
                                                            <img src="{{ $iconUrl }}" class="w-8 h-8 object-contain mb-2 opacity-80 group-hover:opacity-100 drop-shadow-sm" />
                                                        @endif
                                                        <span class="text-sm font-medium leading-tight">{{ $optionLabel }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                    </div>
                                @else
                                    <x-form.configoption :config="$configOption" :name="'checkoutConfig.' . $configOption->name" class="w-full bg-background-secondary/40 backdrop-blur-sm border-white/10 focus:border-primary focus:ring-primary rounded-xl px-4 py-2.5">
                                            @if ($configOption->type == 'select')
                                                @foreach ($configOption->options as $configOptionValue => $configOptionValueName)
                                                    <option value="{{ $configOptionValue }}">{{ $configOptionValueName }}</option>
                                                @endforeach
                                            @endif
                                    </x-form.configoption>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-4 sticky top-6">
            <div class="rounded-2xl border border-white/10 bg-background-secondary/60 backdrop-blur-xl shadow-2xl p-6">
                <h2 class="text-xl font-bold text-base mb-6 flex items-center gap-2">
                    {{ __('product.order_summary') }}
                </h2>

                @if(count($configOptions) > 0 || count($checkoutConfig) > 0)
                    <div class="mb-6 space-y-3">
                        @foreach ($product->configOptions as $configOption)
                            @if (isset($configOptions[$configOption->id]))
                                @php $selected = $configOption->children->firstWhere('id', $configOptions[$configOption->id]); @endphp
                                @if ($selected)
                                    <div class="flex justify-between items-center text-sm group">
                                        <span class="text-color-muted group-hover:text-base transition-colors">{{ $configOption->name }}:</span>
                                        <span class="font-medium text-base">{{ $selected->name }}</span>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                        
                        @foreach ($this->getCheckoutConfig() as $configOption)
                            @php $configOption = (object) $configOption; @endphp
                            @if (isset($checkoutConfig[$configOption->name]))
                                <div class="flex justify-between items-center text-sm group">
                                    <span class="text-color-muted group-hover:text-base transition-colors">{{ $configOption->label ?? $configOption->name }}</span>
                                    <span class="font-medium text-base">{{ $configOption->options[$checkoutConfig[$configOption->name]] ?? $checkoutConfig[$configOption->name] }}</span>
                                </div>
                            @endif
                        @endforeach
                        
                        <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>
                    </div>
                @endif

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-color-muted">{{ __('Subtotal') }}</span>
                        <span class="font-semibold text-base">{{ $total->format($total->subtotal) }}</span>
                    </div>
                    
                    @if ($total->total_tax > 0)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-color-muted">{{ \App\Classes\Settings::tax()->name }} ({{ \App\Classes\Settings::tax()->rate }}%)</span>
                            <span class="font-semibold text-base">{{ $total->formatted->total_tax }}</span>
                        </div>
                    @endif

                    <div class="pt-4 mt-4 border-t border-dashed border-white/10">
                        <div class="flex justify-between items-end">
                            <span class="font-bold text-color-muted">Total</span>
                            <span class="text-3xl font-bold text-primary">{{ $total }}</span>
                        </div>
                        @if ($total->setup_fee && $plan->type == 'recurring')
                            <p class="text-xs text-right text-color-muted mt-1 opacity-70">
                                Renewal: {{ $total->format($total->price) }} / {{ $plan->billing_period }} {{ trans_choice(__('services.billing_cycles.' . $plan->billing_unit), $plan->billing_period) }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
                        <button wire:click="checkout" wire:loading.attr="disabled" 
                            class="w-full rounded-xl bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove>{{ __('product.checkout') }}</span>
                            <span wire:loading><x-ri-loader-4-fill class="animate-spin size-5" /></span>
                        </button>
                    @else
                        <div class="w-full p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-center font-bold backdrop-blur-sm">
                            {{ __('Out of Stock') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>