<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb class="mb-6" />

    <h1 class="text-3xl lg:text-4xl font-bold text-color-base mt-4 mb-8">
        {{ __('account.credits') }}
    </h1>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8">
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-coins-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.credits') }}</h2>
                <p class="text-sm text-color-muted">Your available credit balances across all currencies</p>
            </div>
        </div>

        @if (Auth::user()->credits->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                @foreach (Auth::user()->credits as $credit)
                    <div class="bg-background/50 border border-neutral/50 rounded-xl p-6 text-center shadow-sm transition-all duration-300 hover:scale-[1.01] hover:shadow-lg hover:border-primary/50">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-primary/10 p-3 rounded-xl shadow-sm">
                                <x-ri-money-dollar-circle-line class="size-6 text-primary" />
                            </div>
                        </div>
                        <h5 class="text-xl font-bold text-color-base mb-2">{{ $credit->currency->code }}</h5>
                        <p class="text-2xl text-primary font-bold">{{ $credit->formattedAmount }}</p>
                        <span class="inline-block mt-2 px-3 py-1 text-xs font-medium text-primary bg-primary/20 rounded-full">
                            Available
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-warning/5 border border-warning/20 rounded-xl p-8 text-center">
                <div class="bg-warning/10 p-4 rounded-xl w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                    <x-ri-coins-line class="size-10 text-warning" />
                </div>
                <h3 class="text-xl font-semibold text-warning mb-2">{{ __('account.no_credit') }}</h3>
            </div>
        @endif
    </div>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg" style="animation-duration: 0.8s;">
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-add-circle-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.add_credit') }}</h2>
                <p class="text-sm text-color-muted">Top up your account with additional credits</p>
            </div>
        </div>

        <form wire:submit.prevent="addCredit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> 
                <x-form.select 
                    name="currency" 
                    :label="__('account.input.currency')" 
                    wire:model="currency" 
                    required
                    class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary"
                >
                    <option value="" disabled selected>{{ __('account.input.currency') }}</option> 
                    @foreach(\App\Models\Currency::all() as $currency)
                        <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                    @endforeach
                </x-form.select>

                <x-form.input 
                    x-mask:dynamic="$money($input, '.', '', 2)" 
                    name="amount" 
                    type="text"
                    :label="__('account.input.amount')" 
                    :placeholder="__('account.input.amount')"
                    wire:model="amount" 
                    required 
                    class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary"
                />

                <div class="md:col-span-2"> 
                    <x-form.select 
                        name="gateway" 
                        :label="__('product.payment_method')" 
                        wire:model="gateway" 
                        required
                        class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary"
                    >
                        <option value="" disabled selected>{{ __('product.payment_method') }}</option> 
                        @foreach(\App\Models\Gateway::all() as $gateway)
                            <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end mt-8">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                >
                    <x-ri-add-line class="size-4 mr-2" />
                    {{ __('account.add_credit') }}
                </button>
            </div>
        </form>
        
    </div>
</div>