<div class="flex flex-col gap-1">
    @switch($config->type)
        @case('select')
            <x-form.select name="{{ $name }}" :label="__($config->label ?? $config->name)" :required="$config->required ?? false"
                :selected="config('configs.' . $config->name)" :multiple="$config->multiple ?? false"
                wire:model.live="{{ $name }}" :placeholder="$config->placeholder ?? ''">
                {{ $slot }}
            </x-form.select>
        @break

        @case('slider')
            <div x-data="{
                options: @js($config->children->map(fn($child) => [
                    'name' => $child->name,
                    'value' => $child->id,
                    'price' => ($showPriceTag && $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) 
                               ? (string)$child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) 
                               : 'Free'
                ])),
                showPriceTag: @js($showPriceTag),
                selectedOption: 0,
                backendOption: $wire.entangle('{{ $name }}').live,
                progress: '0%',
                currentPrice: '',
                optionCount: 0,

                init() {
                    this.optionCount = this.options.length;
                    const initialValue = this.$wire.get('{{ $name }}');
                    const foundIndex = this.options.findIndex(opt => opt.value == initialValue);
                    this.selectedOption = (foundIndex !== -1) ? foundIndex : 0;
                    this.updateSlider();

                    $watch('selectedOption', (index) => {
                        this.updateSlider();
                        const debouncedUpdate = Alpine.debounce(() => {
                            this.backendOption = this.options[index].value;
                        }, 150);
                        debouncedUpdate();
                    });
                },

                updateSlider() {
                    let index = parseInt(this.selectedOption);
                    if (isNaN(index)) index = 0;
                    
                    if (this.optionCount > 1) {
                        this.progress = `${(index / (this.optionCount - 1)) * 100}%`;
                    } else {
                        this.progress = '0%';
                    }
                    
                    if (this.optionCount > 0) {
                        this.currentPrice = this.options[index]?.price ?? '';
                    }
                },

                setOptionValue(index) {
                    this.selectedOption = parseInt(index);
                }
            }" class="flex flex-col w-full">

                <label for="{{ $name }}" class="mb-4 text-xl font-bold text-color-base">
                    {{ $config->label ?? $config->name }}
                    @if($config->required ?? false)
                        <span class="text-error">*</span>
                    @endif
                </label>

                <div class="relative w-full pt-8 h-5 mt-2" wire:ignore>

                    <div class="absolute inset-y-0 my-auto w-full h-1.5 z-10">
                        
                        <div class="absolute w-full h-full rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        
                        <template x-for="(option, index) in options" :key="index">
                            <div x-show="index > 0 && index < optionCount - 1"
                                 class="absolute w-px h-full bg-gray-400 dark:bg-gray-500" 
                                 :style="{ left: `calc(${(index / (optionCount - 1)) * 100}% - 0.5px)` }">
                            </div>
                        </template>
                    </div>

                    <div class="absolute inset-y-0 my-auto h-1.5 rounded-full bg-primary z-20" 
                         :style="{ width: progress }">
                    </div>

                    <div class="absolute -top-0 flex flex-col items-center z-30"
                         style="transform: translateX(-50%);"
                         :style="{ left: progress }"
                         x-show="optionCount > 0"
                         x-transition
                         x-cloak>
                         
                        <div x-show="showPriceTag && currentPrice"
                             class="absolute bottom-full mb-2 px-2 py-1 text-xs font-semibold text-white rounded shadow-md whitespace-nowrap"
                             :class="currentPrice === 'Free' ? 'bg-blue-500' : 'bg-primary'"
                             x-text="currentPrice">
                        </div>
                        
                        <div class="w-5 h-5 bg-white rounded-full shadow-md border-2 border-primary"></div>
                    </div>

                    <input type="range" 
                           min="0" :max="optionCount > 1 ? optionCount - 1 : 0" 
                           x-model="selectedOption"
                           class="
                               absolute inset-0 w-full h-full appearance-none cursor-pointer bg-transparent focus:outline-none z-40
                               [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:w-5
                               [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:h-5 [&::-moz-range-thumb]:w-5
                           "
                           name="{{ $name }}" id="{{ $name }}" :disabled="optionCount <= 1" />
                </div>
                
                <ul class="flex justify-between w-full mt-2" x-show="optionCount > 1">
                    <template x-for="(option, index) in options" :key="index">
                        <li class="flex-1" 
                            :class="{
                                'text-left': index == 0,
                                'text-right': index == optionCount - 1,
                                'text-center': index > 0 && index < optionCount - 1
                            }">
                            <button type="button" 
                                    @click="setOptionValue(index)"
                                    class="text-sm font-medium text-center transition-colors cursor-pointer"
                                    :class="selectedOption == index ? 'text-primary dark:text-white font-bold' : 'text-color-muted dark:text-gray-400'">
                                <span x-text="option.name"></span>
                            </button>
                        </li>
                    </template>
                </ul>
                
                <div class="text-left" x-show="optionCount === 1">
                     <span class="text-sm font-medium text-color-muted dark:text-gray-400" x-text="options[0].name"></span>
                </div>
            </div>
        @break

        @case('text')
        @case('password')
        @case('email')
        @case('number')
        @case('color')
        @case('file')
            <x-form.input name="{{ $name }}" :type="$config->type" :label="__($config->label ?? $config->name)"
                :placeholder="$config->default ?? ''" :required="$config->required ?? false" wire:model.live="{{ $name }}" :placeholder="$config->placeholder ?? ''" />
        @break

        @case('checkbox')
            <x-form.checkbox name="{{ $name }}" type="checkbox" :label="__($config->label ?? $config->name) . (($showPriceTag && $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' - ' . $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '')"
                :required="$config->required ?? false" wire:model.live="{{ $name }}" />
        @break

        @case('radio')
            <x-form.radio name="{{ $name }}" :label="__($config->label ?? $config->name)" :required="$config->required ?? false" wire:model.live="{{ $name }}">
                {{ $slot }}
            </x-form.radio>
        @break

        @default
    @endswitch
    @isset($config->description)
        @isset($config->link)
            <a href="{{ $config->link }}" class="text-xs text-primary hover:underline group">
                {{ $config->description }}
                <svg class="ml-1 size-3 inline-block -rotate-45 group-hover:rotate-0 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>
        @else
            <p class="text-xs text-color-muted">{{ $config->description }}</p>
        @endisset
    @endisset
</div>