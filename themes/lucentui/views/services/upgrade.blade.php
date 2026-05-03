@php
    $publicIconAssetPath = 'assets/lucentui/icons';
    $jsonPath = base_path('themes/lucentui/config.json');

    $iconData = ['ids' => [], 'keywords' => []];

    if (file_exists($jsonPath)) {
        $iconData = json_decode(file_get_contents($jsonPath), true);
    }

    $getIconUrl = function($id, $name) use ($iconData, $publicIconAssetPath) {
        $foundIcon = null;

        if (isset($iconData['ids'][$id])) {
            $foundIcon = $iconData['ids'][$id];
        } else {
            $lowerName = strtolower($name);
            foreach ($iconData['keywords'] ?? [] as $key => $filename) {
                if (str_contains($lowerName, $key)) {
                    $foundIcon = $filename;
                    break; // Fixed: removed $
                }
            }
        }

        if ($foundIcon) {
            return str_starts_with($foundIcon, 'http') ? $foundIcon : asset($publicIconAssetPath . '/' . $foundIcon);
        }

        return null;
    };
@endphp

<div class="container mx-auto px-4 md:px-8 py-10">
    
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-color-base tracking-tight mb-2">
            {{ __('services.upgrade_service', ['service' => $service->product->name]) }}
        </h1>
        <p class="text-color-muted">
            @if($step == 1)
                {{ __('services.upgrade_choose_product') }}
            @else
                {{ __('services.upgrade_choose_config') }}
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <div class="lg:col-span-8 flex flex-col gap-8">

            @if($step == 1)
                <div class="space-y-6">
                    <h2 class="text-lg font-bold text-color-base">{{ __('services.upgrade_choose_product') }}</h2>

                    <div class="grid grid-cols-1 gap-4">
                        
                        <div class="flex items-center gap-4 p-4 rounded-xl border border-white/5 bg-background-secondary/20 opacity-70 cursor-not-allowed">
                            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-background-tertiary/50 border border-white/5 grayscale">
                                @if ($service->product->image)
                                    <img src="{{ Storage::url($service->product->image) }}" alt="{{ $service->product->name }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                    <x-ri-archive-fill class="size-6 text-color-muted" />
                                @endif
                            </div>

                            <div class="flex-grow min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-color-muted">{{ $service->product->name }}</span>
                                    <span class="text-[10px] font-bold text-color-muted bg-white/5 px-2 py-0.5 rounded border border-white/5 uppercase tracking-wide">
                                        {{ __('services.current_plan') }}
                                    </span>
                                </div>
                                <div class="text-sm font-medium text-color-muted">
                                    {{ $service->product->price(null, $service->plan->billing_period, $service->plan->billing_unit, $service->currency_code) }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($service->productUpgrades() as $product)
                                @php
                                    $isSelected = $upgrade == $product->id;
                                @endphp
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="upgrade" value="{{ $product->id }}" wire:model.live="upgrade" class="peer sr-only">
                                    
                                    <div class="flex items-center gap-4 p-4 rounded-xl border border-white/10 bg-background-secondary/40 backdrop-blur-md transition-all duration-200
                                        hover:bg-background-secondary/60 hover:border-white/20
                                        peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:shadow-[0_0_15px_rgba(var(--primary-rgb),0.1)]">
                                        
                                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-background-tertiary/50 border border-white/10 group-hover:border-white/20 transition-colors">
                                            @if ($product->image)
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <x-ri-arrow-up-circle-line class="size-6 text-primary" />
                                            @endif
                                        </div>

                                        <div class="flex-grow min-w-0">
                                            <h3 class="font-bold text-color-base group-hover:text-primary transition-colors mb-1 truncate">
                                                {{ $product->name }}
                                            </h3>
                                            <div class="text-sm font-semibold text-primary">
                                                {{ $product->price(null, $service->plan->billing_period, $service->plan->billing_unit, $service->currency_code) }}
                                            </div>
                                        </div>

                                        <div class="opacity-0 peer-checked:opacity-100 text-primary transition-opacity">
                                            <x-ri-checkbox-circle-fill class="size-6" />
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                    </div>
                </div>
            @else
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-color-base">{{ __('services.upgrade_choose_config') }}</h2>
                        <button wire:click="$set('step', 1)" class="text-sm text-color-muted hover:text-color-base transition-colors flex items-center gap-1">
                            <x-ri-arrow-left-line class="size-4" /> {{ __('Back') }}
                        </button>
                    </div>

                    <div class="space-y-8 rounded-2xl bg-background-secondary/20 backdrop-blur-sm border border-white/5 p-6">
                        @foreach ($upgradeProduct->upgradableConfigOptions as $configOption)
                            @php
                                $showPriceTag = $configOption->children->filter(fn ($value) => !$value->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit)->is_free)->count() > 0;
                            @endphp
                            
                            <div class="space-y-3">
                                <label class="text-sm font-medium text-color-base flex items-center gap-1">
                                    {{ $configOption->name }}
                                    @if ($configOption->required) <span class="text-red-500">*</span> @endif
                                </label>

                                @if ($configOption->type == 'radio')
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
                                                        <img src="{{ $iconUrl }}" class="w-8 h-8 object-contain mb-2 opacity-80 group-hover:opacity-100 drop-shadow-sm" />
                                                    @endif

                                                    <span class="text-sm font-medium leading-tight">
                                                        {{ $child->name }}
                                                    </span>

                                                    @if ($showPriceTag && $child->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit)->available)
                                                        <span class="mt-1 text-[10px] text-color-muted bg-background/30 px-1.5 py-0.5 rounded border border-white/5">
                                                            {{ $child->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    @error('configOptions.' . $configOption->id)
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                @else
                                    <x-form.configoption :config="$configOption" :name="'configOptions.' . $configOption->id" class="w-full bg-background-secondary/40 backdrop-blur-sm border-white/10 focus:border-primary focus:ring-primary rounded-xl px-4 py-2.5" :showPriceTag="$showPriceTag" :plan="$service->plan">
                                        @if ($configOption->type == 'select')
                                            @foreach ($configOption->children as $configOptionValue)
                                                <option value="{{ $configOptionValue->id }}">
                                                    {{ $configOptionValue->name }}
                                                    {{ ($showPriceTag && $configOptionValue->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit)->available) ? ' - ' . $configOptionValue->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit) : '' }}
                                                </option>
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
                <h2 class="text-xl font-bold text-color-base mb-6 flex items-center gap-2">
                    {{ __('services.upgrade_summary') }}
                </h2>

                <div class="space-y-4 mb-6">
                    <div class="p-3 bg-background/30 rounded-xl border border-white/5 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-color-muted">{{ __('services.current_plan') }}</span>
                            <span class="font-medium text-color-base">{{ $service->product->name }}</span>
                        </div>
                        
                        @if($upgrade != $service->product->id)
                            <div class="h-px bg-white/5"></div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-primary font-medium">{{ __('services.new_plan') }}</span>
                                <span class="font-bold text-primary">{{ $upgradeProduct ? $upgradeProduct->name : __('general.select_plan') }}</span>
                            </div>
                        @endif
                    </div>

                    @if(count($configOptions) > 0)
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-color-muted uppercase tracking-wider px-1">Config</p>
                            @foreach ($upgradeProduct->upgradableConfigOptions as $configOption)
                                @if (isset($configOptions[$configOption->id]))
                                    @php
                                        $selectedChildId = $configOptions[$configOption->id];
                                        $selectedOption = $configOption->children->firstWhere('id', $selectedChildId);
                                    @endphp
                                    @if ($selectedOption)
                                        <div class="flex justify-between items-center text-sm group">
                                            <span class="text-color-muted group-hover:text-color-base transition-colors">{{ $configOption->name }}</span>
                                            <div class="text-right">
                                                <span class="font-medium text-color-base block">{{ $selectedOption->name }}</span>
                                                @if (!$selectedOption->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit)->is_free)
                                                    <span class="text-xs text-primary font-semibold">
                                                        {{ $selectedOption->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-2"></div>
                        </div>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-gradient-to-br from-green-500/10 to-green-500/5 border border-green-500/20 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ __('services.total_today') }}</span>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->totalToday() }}</span>
                    </div>
                </div>

                <button wire:click="{{ ($upgradeProduct->upgradableConfigOptions()->count() > 0 && $step == 1) ? 'nextStep' : 'doUpgrade' }}" 
                        wire:loading.attr="disabled"
                        class="w-full rounded-xl bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                    
                    <span wire:loading.remove>
                        @if($upgradeProduct && $upgradeProduct->upgradableConfigOptions()->count() > 0 && $step == 1)
                            {{ __('services.next_step') }}
                        @else
                            {{ __('services.upgrade') }}
                        @endif
                    </span>
                    
                    <span wire:loading.remove>
                        @if($upgradeProduct && $upgradeProduct->upgradableConfigOptions()->count() > 0 && $step == 1)
                            <x-ri-arrow-right-line class="size-4" />
                        @else
                            <x-ri-rocket-line class="size-4" />
                        @endif
                    </span>

                    <span wire:loading><x-ri-loader-4-fill class="animate-spin size-5" /></span>
                </button>
            </div>
        </div>
    </div>
</div>