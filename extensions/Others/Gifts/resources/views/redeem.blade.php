<div class="container mt-14">
    <div class="mb-4">
        <nav class="flex items-center space-x-2 text-sm text-base/70">
            <a href="{{ route('dashboard') }}" class="hover:text-primary-100 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('gifts.redeem') }}" class="hover:text-primary-100 transition-colors">Redeem Gift Code</a>
            @if($code)
                <span>/</span>
                <span class="text-primary-100">{{ strtoupper($code) }}</span>
            @endif
        </nav>
    </div>
    <div class="px-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-2xl font-bold text-primary-100">Redeem Gift Code</h3>
                <p class="text-sm text-base/70">Enter your gift code to redeem coupons, credits, services, or discounts.</p>
            </div>
        </div>

        <div class="bg-background-secondary border border-neutral rounded-lg p-6">
            @if($message)
                <div class="mb-4 p-4 rounded-lg {{ $messageType === 'success' ? 'bg-success/10 border border-success text-success' : 'bg-danger/10 border border-danger text-danger' }}">
                    {{ $message }}
                </div>
            @endif

            @if(!$showSelection)
                <form wire:submit.prevent="checkCode">
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-primary-100 mb-2">
                            Gift Code
                        </label>
                        <input
                            type="text"
                            id="code"
                            wire:model="code"
                            class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 placeholder-base/50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Enter your gift code"
                            autocomplete="off"
                            autofocus
                        >
                        @error('code')
                            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-button.primary type="submit" class="w-full">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Check Code
                    </x-button.primary>
                </form>
            @endif

            @if($showSelection && $gift)
                <div class="mb-6">
                    <div class="mb-4 p-4 bg-background-primary rounded-lg border border-neutral">
                        <p class="text-sm font-semibold text-primary-100 mb-1">Gift Code: <span class="font-mono">{{ $gift->code }}</span></p>
                        <p class="text-sm text-base/70">{{ $gift->description ?? ucfirst($gift->type) . ' Gift' }}</p>
                    </div>

                    @if($gift->type === 'service')
                        <div class="mb-4">
                            <label for="selected_product_id" class="block text-sm font-medium text-primary-100 mb-2">
                                Select Product
                            </label>
                            <select
                                id="selected_product_id"
                                wire:model.live="selectedProductId"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">Choose a product...</option>
                                @foreach($this->products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProductId')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($selectedProductId)
                            <div class="mb-4">
                                <label for="selected_plan_id" class="block text-sm font-medium text-primary-100 mb-2">
                                    Select Plan
                                </label>
                                <select
                                    id="selected_plan_id"
                                    wire:model="selectedPlanId"
                                    class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                >
                                    <option value="">Choose a plan...</option>
                                    @php
                                        $plans = \App\Models\Plan::where('priceable_type', \App\Models\Product::class)
                                            ->where('priceable_id', $selectedProductId)
                                            ->get();
                                    @endphp
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedPlanId')
                                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    @endif

                    @if($gift->type === 'extension' || $gift->type === 'upgrade')
                        <div class="mb-4">
                            <label for="selected_service_id" class="block text-sm font-medium text-primary-100 mb-2">
                                Select Service
                            </label>
                            <select
                                id="selected_service_id"
                                wire:model="selectedServiceId"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">Choose a service...</option>
                                @foreach($this->services as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->product->name }} - {{ $service->plan->name ?? 'N/A' }}
                                        @if($service->expires_at)
                                            (Expires: {{ $service->expires_at->format('Y-m-d') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedServiceId')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($gift->type === 'extension' && $gift->allow_extension_range)
                        <div class="mb-4">
                            <label for="selected_extension_period" class="block text-sm font-medium text-primary-100 mb-2">
                                Extension Period ({{ $gift->extension_min_period }} - {{ $gift->extension_max_period }} {{ $gift->extension_unit }}(s))
                            </label>
                            <input
                                type="number"
                                id="selected_extension_period"
                                wire:model="selectedExtensionPeriod"
                                min="{{ $gift->extension_min_period }}"
                                max="{{ $gift->extension_max_period }}"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Enter extension period"
                            >
                            @error('selectedExtensionPeriod')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($gift->type === 'coupon' && $gift->allow_coupon_selection)
                        <div class="mb-4">
                            <label for="selected_coupon_id" class="block text-sm font-medium text-primary-100 mb-2">
                                Select Coupon
                            </label>
                            <select
                                id="selected_coupon_id"
                                wire:model="selectedCouponId"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">Choose a coupon...</option>
                                @foreach($this->coupons as $coupon)
                                    <option value="{{ $coupon->id }}">{{ $coupon->code }}</option>
                                @endforeach
                            </select>
                            @error('selectedCouponId')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($gift->type === 'credit' && $gift->allow_credit_range)
                        <div class="mb-4">
                            <label for="selected_credit_amount" class="block text-sm font-medium text-primary-100 mb-2">
                                Credit Amount ({{ $gift->credit_min_amount }} - {{ $gift->credit_max_amount }})
                            </label>
                            <input
                                type="number"
                                id="selected_credit_amount"
                                wire:model="selectedCreditAmount"
                                min="{{ $gift->credit_min_amount }}"
                                max="{{ $gift->credit_max_amount }}"
                                step="0.01"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Enter credit amount"
                            >
                            @error('selectedCreditAmount')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($gift->type === 'credit' && $gift->allow_currency_selection)
                        <div class="mb-4">
                            <label for="selected_currency_code" class="block text-sm font-medium text-primary-100 mb-2">
                                Select Currency
                            </label>
                            <select
                                id="selected_currency_code"
                                wire:model="selectedCurrencyCode"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">Choose a currency...</option>
                                @foreach($this->currencies as $currency)
                                    <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                                @endforeach
                            </select>
                            @error('selectedCurrencyCode')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if($gift->type === 'discount' && $gift->allow_discount_range)
                        <div class="mb-4">
                            <label for="selected_discount_amount" class="block text-sm font-medium text-primary-100 mb-2">
                                Discount Amount ({{ $gift->discount_min_amount }} - {{ $gift->discount_max_amount }})
                            </label>
                            <input
                                type="number"
                                id="selected_discount_amount"
                                wire:model="selectedDiscountAmount"
                                min="{{ $gift->discount_min_amount }}"
                                max="{{ $gift->discount_max_amount }}"
                                step="0.01"
                                class="w-full px-4 py-2 bg-background-primary border border-neutral rounded-lg text-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Enter discount amount"
                            >
                            @error('selectedDiscountAmount')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <x-button.secondary wire:click="$set('showSelection', false)" class="flex-1">
                            Cancel
                        </x-button.secondary>
                        <x-button.primary wire:click="redeem" class="flex-1">
                            Redeem Gift
                        </x-button.primary>
                    </div>
                </div>
            @endif

            <div class="mt-8 border-t border-neutral pt-8">
                <h4 class="text-sm font-semibold text-primary-100 mb-4 mt-6">What can you redeem?</h4>
                <div class="space-y-2 text-sm text-base/70">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Coupon Codes:</strong> Get discount codes that you can use during checkout</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Account Credits:</strong> Add credit to your account balance</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Free Services:</strong> Activate free services directly in your account</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Discounts:</strong> Get percentage or fixed amount discounts on your purchases</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Subscription Extension:</strong> Extend your active services by a specified period</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Product Upgrade:</strong> Upgrade your service to a better plan</span>
                    </div>
                </div>
            </div>

            @if($this->redeemedGifts->count() > 0)
                <div class="mt-8 border-t border-neutral pt-8">
                    <h4 class="text-sm font-semibold text-primary-100 mb-4 mt-6">Your Redeemed Gifts</h4>
                    <div class="space-y-3">
                        @foreach($this->redeemedGifts as $redemption)
                            <div class="bg-background-primary border border-neutral rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-sm font-semibold text-primary-100">{{ $redemption->gift->code }}</span>
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-primary/20 text-primary-100 capitalize">
                                                {{ $redemption->gift->type }}
                                            </span>
                                        </div>
                                        @if($redemption->gift->description)
                                            <p class="text-sm text-base/70 mb-2">{{ $redemption->gift->description }}</p>
                                        @endif
                                        @if($redemption->notes)
                                            <p class="text-sm text-base/70">{{ $redemption->notes }}</p>
                                        @endif
                                        <p class="text-xs text-base/50 mt-2">
                                            Redeemed on {{ $redemption->redeemed_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
