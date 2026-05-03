<div class="container mx-auto px-8 py-8" 
     x-data 
     @refresh-cart.window="setTimeout(() => $wire.$refresh(), 100)">

    <div class="flex items-center gap-4 mb-2">
        <x-ri-shopping-cart-2-fill class="size-8 text-primary" />
        <h1 class="text-3xl font-extrabold text-color-base tracking-tight">
            Your Cart
        </h1>
        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full font-semibold text-sm">
            {{ Cart::items()->count() }}
        </span>
    </div>

    <div class="border-t border-dashed border-neutral pt-4 mt-4 text-sm text-color-muted mb-6"></div>

    <div class="grid md:grid-cols-4 gap-8 lg:gap-12 items-start">

        <div class="flex flex-col col-span-3 gap-6">

            @if (Cart::items()->count() === 0)
                <div class="bg-background-secondary/70 backdrop-blur-md rounded-xl shadow-lg p-8 text-center border border-neutral/50">
                    <x-ri-inbox-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
                    <h1 class="text-3xl font-bold text-color-base mb-4">
                        {{ __('product.empty_cart') }}
                    </h1>
                    <p class="text-color-muted mb-6">Your shopping cart is currently empty. Start adding some products!</p>
                    
                    <a href="{{ route('home') }}" 
                       wire:navigate 
                       class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg">
                        <x-ri-shopping-bag-line class="size-5" />
                        Continue Shopping
                    </a>
                </div>
            @endif

            <!-- Add Buzz's Upsell extenstion support -->
            @includeWhen(View::exists('upsells::components.spend-minimum-top'), 'upsells::components.spend-minimum-top', [
                'settings' => $upsellSettings ?? null,
                'spendMinimumUpsells' => $spendMinimumUpsells ?? null
            ])

            @foreach (Cart::items() as $item)
                <div class="bg-background-secondary/70 backdrop-blur-md rounded-xl shadow-lg p-6 border border-neutral/50 hover:shadow-xl transition-all duration-300 hover:border-primary/30">
                    
                    <div class="flex flex-col lg:flex-row gap-6">
                        
                        <div class="flex-grow flex flex-col sm:flex-row items-start gap-4">
                            
                            @if ($item->product->image)
                                <div class="flex-shrink-0 relative overflow-hidden rounded-xl w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-primary/10 to-primary/5 p-2">
                                    <img src="{{ Storage::url($item->product->image) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-full h-full object-contain object-center">
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <h2 class="text-xl font-bold text-color-base mb-3">{{ $item->product->name }}</h2>
                                
                                @if (!empty($item->config_options))
                                    <div class="space-y-2">
                                        @foreach ($item->config_options as $option)
                                            <div class="group p-2.5 bg-background-tertiary/30 hover:bg-background-tertiary/50 rounded-lg border border-neutral/20 transition-all duration-200">
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                                    <span class="text-xs text-color-muted">{{ $option['option_name'] }}</span>
                                                    <span class="hidden sm:inline text-color-muted/40">•</span>
                                                    <span class="text-sm font-medium text-color-base">{{ $option['value_name'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div class="lg:hidden mt-3">
                                    <div class="text-2xl font-bold text-primary">
                                        {{ $item->price->format($item->price->total * $item->quantity) }}
                                    </div>
                                    @if ($item->quantity > 1)
                                        <span class="text-sm text-color-muted">{{ $item->price }} {{ __('each') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="hidden lg:flex flex-col items-end justify-between gap-4 min-w-[200px]">
                            <div class="text-right">
                                <div class="text-2xl font-bold text-primary mb-1">
                                    {{ $item->price->format($item->price->total * $item->quantity) }}
                                </div>
                                @if ($item->quantity > 1)
                                    <span class="text-sm text-color-muted">{{ $item->price }} {{ __('each') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mt-6 pt-5 border-t border-neutral/30">
                        
                        @if ($item->product->allow_quantity == 'combined')
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-color-muted mr-2">Quantity:</span>
                                <x-button.secondary wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                    class="p-2 rounded-lg flex items-center justify-center size-9 !min-w-0 !min-h-0 hover:bg-neutral/40 transition-all duration-200">
                                    <x-ri-subtract-line class="size-4" />
                                </x-button.secondary>

                                <x-form.input class="text-center w-16 py-2 font-semibold" 
                                              disabled 
                                              value="{{ $item->quantity }}" 
                                              name="quantity-{{ $item->id }}" 
                                              divClass="!mt-0 !w-auto" />

                                <x-button.secondary wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }});"
                                                    class="p-2 rounded-lg flex items-center justify-center size-9 !min-w-0 !min-h-0 hover:bg-neutral/40 transition-all duration-200">
                                    <x-ri-add-line class="size-4" />
                                </x-button.secondary>
                            </div>
                        @else
                            <div></div>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('products.checkout', [$item->product->category, $item->product, 'edit' => $item->id]) }}"
                               wire:navigate
                               class="inline-flex justify-center items-center px-4 py-2.5 border border-neutral/50 rounded-lg text-sm font-medium text-color-base hover:bg-neutral/30 hover:border-neutral transition-all duration-200">
                                <x-ri-edit-line class="size-4 mr-2" />
                                {{ __('product.edit') }}
                            </a>
                            
                            <x-button.danger wire:click="removeProduct({{ $item->id }})" 
                                             class="inline-flex justify-center items-center px-4 py-2.5 rounded-lg text-sm transition-all duration-200">
                                <x-loading target="removeProduct({{ $item->id }})" class="mr-2" />
                                <div wire:loading.remove wire:target="removeProduct({{ $item->id }})" class="flex items-center">
                                    <x-ri-delete-bin-line class="size-4 mr-2" />
                                    {{ __('product.remove') }}
                                </div>
                            </x-button.danger>
                        </div>
                    </div>
                </div>

                <!-- Add Buzz's Upsell extenstion support -->
                @includeWhen(View::exists('upsells::components.cart-upsells'), 'upsells::components.cart-upsells', [
                    'item' => $item,
                    'settings' => $upsellSettings ?? null,
                    'getUpsellsForProduct' => $getUpsellsForProduct ?? null
                ])
                
            @endforeach
        </div>

        <div class="flex flex-col gap-6 w-full col-span-3 md:col-span-1 sticky top-8 md:top-20">

            <!-- Add Buzz's Upsell extenstion support -->
            @includeWhen(View::exists('upsells::components.spend-minimum-sidebar'), 'upsells::components.spend-minimum-sidebar', [
                'settings' => $upsellSettings ?? null,
                'spendMinimumUpsells' => $spendMinimumUpsells ?? null
            ])

            @if (Cart::items()->count() > 0)
                
                <div class="bg-background-secondary/70 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-neutral/50">
                        <div class="bg-primary/10 text-primary p-2.5 rounded-full">
                            <x-ri-file-list-3-fill class="size-5" />
                        </div>
                        <h2 class="text-2xl font-bold text-color-base">
                            {{ __('product.order_summary') }}
                        </h2>
                    </div>

                    <div class="space-y-4">
                        
                        @if(!$coupon)
                            <div class="bg-background-tertiary/30 rounded-xl p-4 border border-neutral/30">
                                <label class="block text-sm font-medium text-color-base mb-2">{{ __('Coupon Code') }}</label>
                                <div class="mb-3">
                                    <x-form.input wire:model="coupon" 
                                                  name="coupon" 
                                                  placeholder="Enter code" 
                                                  class="h-10 px-3 py-2.5 text-sm font-medium w-full" 
                                                  divClass="!mt-0 w-full" />
                                </div>
                                <div>
                                    <x-button.primary wire:click="applyCoupon" class="w-full h-10 px-4 rounded-xl" wire:loading.attr="disabled">
                                        <x-loading target="applyCoupon" class="size-4" />
                                        <span wire:loading.remove wire:target="applyCoupon" class="text-sm font-medium">
                                            {{ __('product.apply') }}
                                        </span>
                                    </x-button.primary>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-between items-center bg-green-500/10 border border-green-500/30 rounded-xl p-3">
                                <div class="flex items-center gap-2">
                                    <x-ri-coupon-3-fill class="size-5 text-green-600" />
                                    <div>
                                        <h4 class="font-semibold text-color-base">{{ $coupon->code }}</h4>
                                        <span class="text-xs text-green-600">{{ __('Applied') }}</span>
                                    </div>
                                </div>
                                <x-button.secondary wire:click="removeCoupon" class="p-2 rounded-full size-8 !min-w-0 !min-h-0 hover:scale-110 transition-transform duration-200">
                                    <x-ri-close-line class="size-4" />
                                </x-button.secondary>
                            </div>
                        @endif

                        <div class="flex justify-between items-center p-3 bg-background-tertiary/30 rounded-xl border border-neutral/30">
                            <div class="flex items-center gap-2">
                                <x-ri-price-tag-3-line class="size-4 text-color-muted" />
                                <h4 class="font-semibold text-color-base text-sm">{{ __('invoices.subtotal') }}</h4>
                            </div>
                            <span class="font-semibold text-color-base">{{ $total->format($total->subtotal) }}</span>
                        </div>

                        @if ($total->tax > 0)
                            <div class="flex justify-between items-center p-3 bg-background-tertiary/30 rounded-xl border border-neutral/30">
                                <div class="flex items-center gap-2">
                                    <x-ri-percent-line class="size-4 text-color-muted" />
                                    <h4 class="font-semibold text-color-base text-sm">
                                        {{ \App\Classes\Settings::tax()->name }} ({{ \App\Classes\Settings::tax()->rate }}%)
                                    </h4>
                                </div>
                                <span class="font-semibold text-color-base">{{ $total->format($total->tax) }}</span>
                            </div>
                        @endif

                        <div class="border-t border-dashed border-neutral/50 pt-4 mt-4">
                            <div class="flex flex-col items-start p-4 bg-gradient-to-r from-green-400/20 via-green-500/10 to-green-400/20 rounded-xl border border-green-400/30 shadow-md relative overflow-hidden">
                                <svg class="absolute -top-4 -right-4 w-20 h-20 opacity-25 pointer-events-none z-0" viewBox="0 0 80 80" fill="none">
                                    <defs>
                                        <radialGradient id="cornerGradient" cx="0.7" cy="0.3" r="1">
                                            <stop offset="0%" stop-color="#22c55e" stop-opacity="0.7"/>
                                            <stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>
                                        </radialGradient>
                                    </defs>
                                    <circle cx="70" cy="10" r="40" fill="url(#cornerGradient)" />
                                    <circle cx="60" cy="20" r="10" fill="#22c55e" fill-opacity="0.3"/>
                                    <rect x="50" y="0" width="20" height="20" rx="6" fill="#22c55e" fill-opacity="0.15"/>
                                </svg>
                                <div class="flex items-center gap-2 z-10">
                                    <x-ri-money-dollar-circle-fill class="size-5 text-green-600" />
                                    <h4 class="text-lg font-semibold text-green-600">{{ __('invoices.total') }}</h4>
                                </div>
                                <span class="text-2xl font-bold text-green-600 z-10">{{ $total->format($total->total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-background-secondary/70 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50">
                    @if($total->total > 0)
                        
                        @if(count($gateways ?? []) > 1)
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-color-base mb-2">{{ __('product.payment_method') }}</label>
                                <x-form.select wire:model.live="gateway" 
                                               name="gateway"
                                               class="w-full text-color-base bg-background-tertiary/30 border-neutral/50 focus:border-primary focus:ring-primary focus:ring-2 focus:ring-primary/20 rounded-xl px-4 py-2.5 transition-colors duration-200" 
                                               divClass="!mt-0">
                                    @foreach ($gateways as $gateway)
                                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                        @endif

                        @if(Auth::check() && Auth::user()->credits()->where('currency_code', Cart::items()->first()->price->currency->code)->exists() && Auth::user()->credits()->where('currency_code', Cart::items()->first()->price->currency->code)->first()->amount > 0)
                            <div class="mb-5 p-4 bg-primary/5 border border-primary/20 rounded-xl">
                                <x-form.checkbox wire:model="use_credits" name="use_credits" label="{{ __('product.use_credits') }}" />
                            </div>
                        @endif
                    @endif

                    @if(config('settings.tos'))
                        <div class="mb-6 p-4 bg-background-tertiary/30 border border-neutral/30 rounded-xl">
                            <x-form.checkbox wire:model="tos" name="tos">
                                {{ __('product.tos') }}
                                <a href="{{ config('settings.tos') }}" target="_blank" class="text-primary hover:text-primary/80 font-medium ml-1 underline">
                                    {{ __('product.tos_link') }}
                                </a>
                            </x-form.checkbox>
                        </div>
                    @endif

                    <x-button.primary wire:click="checkout" 
                                      class="group/btn w-full inline-flex items-center justify-center gap-3 py-3.5 text-lg font-semibold rounded-xl shadow-lg hover:shadow-primary/30 transition-all duration-300 hover:scale-105" 
                                      wire:loading.attr="disabled">
                        <x-loading target="checkout" class="size-5" />
                        <span wire:loading.remove wire:target="checkout" class="flex items-center gap-2">
                            {{ __('product.checkout') }}
                            <x-ri-arrow-right-fill class="size-5 transform transition-transform duration-300 group-hover/btn:translate-x-1" />
                        </span>
                    </x-button.primary>
                </div>
            @endif
        </div>

    </div>
</div>