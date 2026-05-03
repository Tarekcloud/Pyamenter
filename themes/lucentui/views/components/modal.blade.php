@props([
    'title' => '',
    'closable' => true,
    'closeTrigger' => '',
    'open' => false,
    'width' => 'max-w-3xl'
])

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }">
    <template x-teleport="body">
        
        <div
            class="fixed inset-0 z-30 flex items-center justify-center overflow-hidden bg-black/30 backdrop-blur-sm"
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                class="px-6 py-5 w-full mx-4 text-left bg-background-secondary/80 border border-white/10 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto {{ $width }}"
                x-show="open"
                x-cloak
                @click.outside="@if($closable && !$closeTrigger) open = false @endif"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <div class="flex justify-between items-center pb-3">
                    <h2 class="text-2xl font-semibold text-primary-100">{{ $title }}</h2>
                    
                    @if ($closable)
                        <div class="cursor-pointer z-50">
                        @if (!$closeTrigger)
                            <button @click="open = false" class="text-primary-100/70 hover:text-primary-100 transition">
                                <x-ri-close-fill class="size-7" />
                            </button>
                        @else
                            {{ $closeTrigger }}
                        @endif
                        </div>
                    @endif
                </div>
                <div class="mt-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>