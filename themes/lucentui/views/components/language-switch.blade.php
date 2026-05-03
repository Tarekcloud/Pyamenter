<div class="p-2">
    <label class="block mb-4 text-sm font-semibold">
    <x-ri-global-line class="inline-block w-4 h-4 mr-2 text-primary"/>
        Locales
    </label>

    <div class="relative" x-data="{ open: false }">
        
        <button 
            type="button"
            @click="open = !open" 
            @click.away="open = false"
            class="flex items-center justify-between w-full px-3 py-2 bg-background-secondary border border-primary/30 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-primary/80 focus:border-primary/80 transition-colors"
        >
            <div class="flex items-center gap-3">
                <img 
                    src="{{ asset('assets/flags/language-' . $currentLocale . '.svg') }}" 
                    alt="{{ $currentLocale }}" 
                    class="w-5 h-5 rounded-sm object-cover"
                >
                
                <span class="text-sm text-base">
                    {{ $locales[$currentLocale] ?? 'Select Language' }}
                </span>
            </div>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full mt-1 bg-background-secondary border border-primary/20 rounded-md shadow-lg max-h-60 overflow-y-auto"
            style="display: none;"
        >
            @foreach($locales as $code => $label)
                <div 
                    wire:click="$set('currentLocale', '{{ $code }}'); open = false"
                    class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-primary/5 transition-colors {{ $currentLocale === $code ? 'bg-primary/10' : '' }}"
                >
                    <img 
                        src="{{ asset('assets/flags/language-' . $code . '.svg') }}" 
                        alt="{{ $code }}"
                        class="w-5 h-5 rounded-sm shadow-sm object-cover" 
                    >
                    <span class="text-xs font-medium text-base">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>