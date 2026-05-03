@props(['properties', 'custom_properties' => []])

@foreach ($custom_properties as $property)
    @switch($property->type)
        @case('date')
        @case('string')
        @case('number')
            <x-form.input :type="$property->type" name="properties.{{ $property->key }}" :label="$property->name" :required="$property->required"
                wire:model="properties.{{ $property->key }}" :value="$properties[$property->key] ?? ''" :disabled="$property->non_editable && isset($properties[$property->key])" />
        @break

        @case('checkbox')
            <x-form.checkbox name="properties.{{ $property->key }}" :label="$property->name" :required="$property->required"
                wire:model="properties.{{ $property->key }}" :checked="$properties[$property->key] ?? false" :disabled="$property->non_editable && isset($properties[$property->key])" />
        @break

        @case('radio')
            <fieldset class="flex flex-col w-full">
                <label class="mb-2 text-sm font-medium text-color-muted">
                    {{ $property->name }}
                    @if ($property->required)
                        <span class="text-error">*</span>
                    @endif
                </label>
                <div class="flex flex-col gap-3 px-2.5 py-2.5 bg-background/30 border border-neutral/20 rounded-xl">
                    @foreach ($property->allowed_values as $value)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-background-tertiary/50 transition-colors">
                            <input 
                                type="radio" 
                                value="{{ $value }}" 
                                id="properties.{{ $property->key }}.{{ Str::slug($value) }}"
                                name="properties.{{ $property->key }}" 
                                @checked($properties[$property->key] === $value ?? false) 
                                @required($property->required)
                                wire:model="properties.{{ $property->key }}" 
                                :disabled="$property->non_editable && isset($properties[$property->key])"
                                class="w-4 h-4 text-primary focus:ring-primary focus:ring-2 focus:ring-primary/30 border-neutral/30 bg-transparent"
                            />
                            <label class="flex-1 text-sm text-color-base cursor-pointer"
                                for="properties.{{ $property->key }}.{{ Str::slug($value) }}">
                                {{ $value }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('properties.' . $property->key)
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </fieldset>
        @break

        @case('select')
            <x-form.select name="properties.{{ $property->key }}" :label="$property->name"
                wire:model="properties.{{ $property->key }}" :required="$property->required" :options="$property->allowed_values" :selected="$properties[$property->key] ?? ''" :disabled="$property->non_editable && isset($properties[$property->key])" />
        @break

        @case('text')
            <x-form.textarea :type="$property->type" name="properties.{{ $property->key }}" :label="$property->name" :required="$property->required"
                wire:model="properties.{{ $property->key }}" :disabled="$property->non_editable && isset($properties[$property->key])">{{ $properties[$property->key] ?? '' }}</x-form.textarea>
        @break

        @default
    @endswitch
@endforeach