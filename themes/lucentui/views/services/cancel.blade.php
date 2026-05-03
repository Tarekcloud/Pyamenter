<div class="flex flex-col gap-6 p-4">
    <p class="text-color-base text-lg font-medium">
        {{ __('services.cancel_are_you_sure') }}
    </p>

    <div class="bg-background-tertiary border border-neutral/50 p-5 rounded-lg shadow-sm">
        <x-form.select name="type" label="{{ __('services.cancel_type') }}" required wire:model.live="type">
            <option value="end_of_period">{{ __('services.cancel_end_of_period') }}</option>
            <option value="immediate">{{ __('services.cancel_immediate') }}</option>
        </x-form.select>
    </div>

    <div class="bg-background-tertiary border border-neutral/50 p-5 rounded-lg shadow-sm">
        <x-form.textarea name="reason" label="{{ __('services.cancel_reason') }}" required wire:model="reason"
            rows="4"
        />
    </div>

    <template x-if="$wire.type === 'immediate'">
        <div class="bg-red-700 text-white p-5 rounded-lg shadow-md flex items-center gap-3 border border-red-500">
            <x-ri-skull-fill class="size-8 flex-shrink-0" /> 
            <div>
                <h3 class="text-xl font-bold mb-1">Hey!</h3>
                <p class="font-medium">
                    {{ __('services.cancel_immediate_warning') }}<br>
                </p>
            </div>
        </div>
    </template>

    <div class="flex justify-end mt-4">
        <x-button.danger wire:confirm="{{ __('Are you sure?') }}" wire:click="cancelService" class="px-6 py-3 font-semibold">
            <span wire:loading.remove wire:target="cancelService">{{ __('services.cancel_are_you_sure') }}</span>
            <x-loading target="cancelService" class="ml-2" />
        </x-button.danger>
    </div>
</div>