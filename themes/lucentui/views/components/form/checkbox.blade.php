@props([
    'name',
    'label' => null,
    'description' => null,
    'value' => '1',
    'id' => null,
    'divClass' => null,
    'size' => 'md', // sm, md, lg
])

<div class="flex items-start gap-3 {{ $divClass ?? '' }}">
    @if($attributes->get('required', false) === false)
        <input type="hidden" name="{{ $name }}" value="0" />
    @endif
    
    <div class="relative flex items-center">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $id ?? $name }}"
            value="{{ $value ?? '1' }}"
            {{ $attributes->except(['label', 'name', 'id', 'value', 'class', 'divClass', 'required', 'disabled', 'description', 'size']) }}
            @class([
                'peer appearance-none border-2 rounded-md transition-all duration-200 ease-in-out',
                'focus:ring-2 focus:ring-offset-0 focus:ring-primary/50 focus:outline-none', // Ring offset 0
                'hover:border-primary/60 disabled:opacity-50 disabled:cursor-not-allowed',
                'checked:bg-primary checked:border-primary checked:hover:bg-primary/90',
                'border-neutral/30 bg-background/50', // Match input bg/border
                'size-4' => $size === 'sm',
                'size-5' => $size === 'md',
                'size-6' => $size === 'lg',
            ])
        />
        
        <svg 
            class="absolute inset-0 size-full p-0.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity duration-200"
            fill="currentColor" 
            viewBox="0 0 20 20"
            aria-hidden="true"
        >
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
    </div>

    <div class="flex-1 min-w-0">
        <label 
            class="block text-sm font-medium text-color-base cursor-pointer select-none {{ $attributes->get('disabled') ? 'opacity-50 cursor-not-allowed' : '' }}" 
            for="{{ $id ?? $name }}"
        >
            @if(isset($label))
                {{ $label }}
                @if($attributes->get('required'))
                    <span class="text-error ml-1" aria-label="required">*</span>
                @endif
            @else
                {{ $slot }}
                @if($attributes->get('required'))
                    <span class="text-error ml-1" aria-label="required">*</span>
                @endif
            @endif
        </label>
        
        @if(isset($description) || $attributes->has('description'))
            <p class="mt-1 text-xs text-color-muted">
                {{ $description ?? $attributes->get('description') }}
            </p>
        @endif

        @error($name)
            <p class="text-error text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>