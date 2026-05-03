<div class="p-2">
    <x-select 
        id="currency-switch" 
        wire:model.live="currentCurrency" 
        :options="$this->currencies" 
        placeholder="Select a currency" 
        class="flex items-center justify-between w-full px-3 py-2 bg-background-secondary border border-primary/30 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-primary/80 focus:border-primary/80 transition-colors"
    />
</div>