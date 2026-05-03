<button 
    {{ $attributes->merge(['class' => 'flex items-center gap-2 justify-center bg-primary text-white text-sm font-semibold hover:bg-primary/90 py-4 lg:py-2 px-8 rounded-xl w-full shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50']) }}>
    @if (isset($type) && $type === 'submit')
        <div role="status" wire:loading>
            <x-ri-loader-5-fill aria-hidden="true" class="size-6 me-2 fill-background animate-spin" />
            <span class="sr-only">Loading...</span>
        </div>
        <div wire:loading.remove>
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</button>
