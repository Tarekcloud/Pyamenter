<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb class="mb-6" />

    <h1 class="text-3xl lg:text-4xl font-bold text-color-base mt-4 mb-8">
        {{ __('account.payment_methods') }}
    </h1>

    @if($setupModalVisible)
    <x-modal :title="__('account.payment_methods')" open="true">
        <x-slot name="closeTrigger">
            <div class="flex gap-4">
                <button wire:click="$set('setupModalVisible', false)" class="text-primary-100">
                    <x-ri-close-fill class="size-6" />
                </button>
            </div>
        </x-slot>

        @if(count($this->gateways) > 1)
        <x-form.select name="gateway" :label="__('account.input.payment_gateway')" wire:model.live="gateway"
            class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary" required>
            @foreach($this->gateways as $gateway)
            <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
            @endforeach
        </x-form.select>
        @elseif(count($this->gateways) === 0)
        <p class="text-sm text-red-500">{{ __('account.no_payment_gateways_available') }}</p>
        @endif

        <x-button.primary class="w-full mt-4" wire:click="createBillingAgreement" wire:loading.attr="disabled">
            <x-loading target="createBillingAgreement" />
            <div wire:loading.remove wire:target="createBillingAgreement">
                {{ __('account.setup_payment_method') }}
            </div>
        </x-button.primary>

        @if ($this->setup)
        <x-modal :title="__('account.setup_payment_method')" open>
            <div class="mt-8">
                {{ $this->setup }}
            </div>
            <x-slot name="closeTrigger">
                <div class="flex gap-4">
                    <button wire:confirm="Are you sure?" wire:click="cancelSetup" wire:loading.attr="disabled"
                        wire:target="cancelSetup" class="text-primary-100">
                        <x-ri-close-fill class="size-6" />
                    </button>
                </div>
            </x-slot>
        </x-modal>
        @endif
    </x-modal>
    @endif

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                    <x-ri-bank-card-line class="size-6 text-primary" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-color-base">{{ __('account.saved_payment_methods') }}</h2>
                    <p class="text-sm text-color-muted">{{ __('account.saved_payment_methods_description') }}</p>
                </div>
            </div>
            @if(count($this->gateways) > 0)
            <x-button.primary class="h-fit !w-fit" wire:click="$set('setupModalVisible', true)"
                wire:loading.attr="disabled" wire:target="setupModalVisible">
                <x-ri-add-line class="size-4" />
                {{ __('account.add_payment_method') }}
            </x-button.primary>
            @endif
        </div>

        @php
        $groupedAgreements = $billingAgreements->groupBy('gateway.name');
        @endphp

        @if($groupedAgreements->count() > 0)
        <div class="pt-6 border-t border-neutral/50">
            @foreach($groupedAgreements as $gatewayName => $agreements)
            <div class="flex items-center justify-between mb-4 mt-6 first:mt-0">
                <div class="flex items-center gap-3">
                    <div class="bg-background/50 border border-neutral/50 rounded-lg overflow-hidden size-9 flex items-center justify-center">
                        @if($agreements->first()?->gateway?->meta?->icon)
                        <img src="{{ $agreements->first()->gateway->meta->icon }}" alt="{{ $gatewayName }}"
                            class="size-9" />
                        @else
                        <x-ri-secure-payment-line class="size-5 text-primary" />
                        @endif
                    </div>
                    <h2 class="text-xl font-semibold text-color-base">{{ $gatewayName }}</h2>
                </div>
                <span class="bg-primary flex items-center justify-center font-semibold rounded-md size-5 text-sm text-white">
                    {{ $agreements->count() }}
                </span>
            </div>

            @foreach($agreements as $agreement)
            <div class="bg-background/50 border border-neutral/50 p-4 rounded-xl mb-4 transition-all duration-300 hover:border-primary/50 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg overflow-hidden flex items-center justify-center">
                            @switch(strtolower($agreement->type))
                            @case('visa')<x-icons.visa class="size-9" />@break
                            @case('mastercard')<x-icons.mastercard class="size-9" />@break
                            @case('amex')<x-icons.amex class="size-9" />@break
                            @case('american express')<x-icons.american-express class="size-9" />@break
                            @case('discover')<x-icons.discover class="size-9" />@break
                            @case('paypal')<x-icons.paypal class="size-9" />@break
                            @case('sepa_debit')<x-icons.sepa class="size-9" />@break
                            @case('ideal')<x-icons.ideal class="size-9" />@break
                            @case('bancontact')<x-icons.bancontact class="size-9" />@break
                            @case('sofort')<x-icons.sofort class="size-9" />@break
                            @case('us_bank_account')
                            @case('bacs_debit')
                            @case('au_becs_debit')<x-icons.bank-debit class="size-9" />@break
                            @default<x-ri-bank-card-line class="size-6 text-primary" />
                            @endswitch
                        </div>
                        <div>
                            <div class="font-semibold text-base text-color-base">
                                {{ $agreement->name }}
                            </div>
                            @if($agreement->expiry)
                            <div class="text-sm text-color-muted">
                                {{ __('account.expires', ['date' => \Carbon\Carbon::parse($agreement->expiry)->format('m/Y')]) }}
                            </div>
                            @endif
                            @if($agreement->services()->count() > 0)
                            <div class="text-xs text-color-muted/70 mt-1">
                                {{ __('account.services_linked', ['count' => $agreement->services()->count()]) }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-button.danger class="!px-2" x-on:click="$store.confirmation.confirm({
                                        title: '{{ __('account.remove_payment_method') }}',
                                        message: '{{ __('account.remove_payment_method_confirm', ['name' => $agreement->name]) }}',
                                        confirmText: '{{ __('account.confirm') }}',
                                        cancelText: '{{ __('account.cancel') }}',
                                        callback: () => $wire.removePaymentMethod('{{ $agreement->ulid }}')
                                    })">
                            <x-ri-delete-bin-line class="size-4" />
                        </x-button.danger>
                    </div>
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
        @else
        <div class="bg-background/5 border border-neutral/20 rounded-xl p-8 text-center mt-6">
            <div class="bg-primary/10 p-4 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                <x-ri-bank-card-line class="size-10 text-primary" />
            </div>
            <p class="text-lg font-semibold text-color-base">{{ __('account.no_saved_payment_methods') }}</p>
        </div>
        @endif
    </div>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg">
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-bill-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.recent_transactions') }}</h2>
            </div>
        </div>

        <div class="space-y-3">
            @foreach ($transactions as $transaction)
            <a href="{{ route('invoices.show', $transaction->invoice) }}" wire:navigate
                class="block bg-background/50 border border-neutral/50 p-4 rounded-xl transition-colors hover:bg-background/100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/10 p-2 rounded-lg">
                            <x-ri-bill-line class="size-5 text-primary" />
                        </div>
                        <div>
                            <span class="font-medium text-color-base">
                                {{ $transaction->transaction_id ? 'Transaction: ' . $transaction->transaction_id :
                                'Transaction ID N/A' }}
                            </span>
                            <div class="text-sm text-color-muted">
                                {{ $transaction->formattedAmount }}
                                using
                                {{ $transaction->gateway ? $transaction->gateway->name : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-color-muted mb-1 block">
                            {{ $transaction->created_at->format('d M Y H:i') }}
                        </span>
                        @if($transaction->status === \App\Enums\InvoiceTransactionStatus::Succeeded)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary/20 border border-primary/30 text-primary font-semibold rounded-full text-xs">
                            <x-ri-checkbox-circle-fill class="size-4" />
                                {{ __('invoices.transaction_statuses.succeeded') }}
                        </span>
                        @elseif($transaction->status === \App\Enums\InvoiceTransactionStatus::Processing)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 border border-yellow-200 text-yellow-800 font-semibold rounded-full text-xs">
                            <x-ri-loader-5-fill class="size-4 mr-1 fill-yellow-600 animate-spin" />
                            {{ __('invoices.transaction_statuses.processing') }}
                        </span>
                        @elseif($transaction->status === \App\Enums\InvoiceTransactionStatus::Failed)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 border border-red-200 text-red-800 font-semibold rounded-full text-xs">
                            <x-ri-close-line class="size-4 mr-1" />
                            {{ __('invoices.transaction_statuses.failed') }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </div>
</div>