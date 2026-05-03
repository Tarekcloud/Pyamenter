@props(['name', 'label' => null, 'required' => false, 'divClass' => null, 'class' => null,'placeholder' => null, 'id' => null, 'type' => null, 'hideRequiredIndicator' => false, 'dirty' => false])
<fieldset class="flex flex-col w-full {{ $divClass ?? '' }}">
    @if ($label)
        <label for="{{ $name }}" class="mt-4 mb-2 text-xl font-bold text-base">
            {{ $label }}
            @if ($required && !$hideRequiredIndicator)
                <span class="text-error">*</span>
            @endif
        </label>
    @endif
    <input type="{{ $type ?? 'text' }}" id="{{ $id ?? $name }}" name="{{ $name }}"
        class="block w-full text-sm text-base bg-background/30 border-2 rounded-xl shadow-sm outline-none backdrop-blur-sm
               
               @error($name) 
                   border-error/70 text-error placeholder:text-error/70
                   focus:border-error/70 focus:ring-2 focus:ring-error/30
               @else 
                   border-neutral/20
                   focus:border-primary/70 focus:ring-2 focus:ring-primary/30
               @enderror

               disabled:bg-background/10 disabled:border-neutral/10 disabled:opacity-50 disabled:cursor-not-allowed 
               transition-all duration-300 ease-in-out {{ $class ?? '' }} @if ($type !== 'color') px-3 py-2.5 @endif"
        placeholder="{{ $placeholder ?? ($label ?? '') }}"
        @if ($dirty && isset($attributes['wire:model'])) wire:dirty.class="!border-warning/50 !ring-2 !ring-warning/30" @endif
        {{ $attributes->except(['placeholder', 'label', 'id', 'name', 'type', 'class', 'divClass', 'required', 'hideRequiredIndicator', 'dirty']) }} @required($required) />
    @error($name)
        <p class="text-error text-xs mt-1">{{ $message }}</p>
    @enderror
</fieldset>