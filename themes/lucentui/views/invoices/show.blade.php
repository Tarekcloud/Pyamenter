<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">

    <div @if ($checkPayment || $invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Processing)->where('created_at', '>=', now()->subDays(1))->count() > 0) wire:poll.5s="checkPaymentStatus" @endif>
        @if ($this->pay || $showPayModal)
            @include('invoices.partials.payment-modal')
        @endif
    </div>

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center justify-center p-2 rounded-lg bg-primary/10 text-primary">
                    <x-ri-bill-fill class="size-6" />
                </span>
                <span class="text-primary text-sm font-medium">{{ $invoice->created_at->format('d M Y') }}</span>
            </div>
            <h1 class="text-3xl font-bold text-color-base leading-tight">
                {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
            </h1>
        </div>

        <div>
            @php
                $statusColor = match($invoice->status) {
                    'paid' => 'success',
                    'cancelled' => 'neutral',
                    'pending' => 'warning',
                    default => 'warning'
                };
                $statusIcon = match($invoice->status) {
                    'paid' => 'ri-checkbox-circle-fill',
                    'cancelled' => 'ri-close-circle-fill',
                    'pending' => 'ri-time-fill',
                    default => 'ri-error-warning-fill'
                };
            @endphp

            <div class="flex items-center gap-2 px-4 py-2 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl">
                <x-dynamic-component :component="$statusIcon" class="size-5 text-{{ $statusColor }}" />
                <span class="text-{{ $statusColor }} font-bold text-sm uppercase tracking-wide">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-sm overflow-hidden h-fit">
                <div class="px-6 py-5 border-b border-neutral/50 flex items-center gap-2">
                    <x-ri-file-list-3-line class="size-5 text-primary" />
                    <h3 class="font-bold text-color-base text-base">Invoice Items</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-background/50 border-b border-neutral/30">
                                <th class="px-6 py-4 text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.item') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.price') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.quantity') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral/30">
                            @foreach ($invoice->items as $item)
                                <tr class="hover:bg-background/30 transition-colors">
                                    <td class="px-6 py-5">
                                        @if(in_array($item->reference_type, ['App\Models\Service', 'App\Models\ServiceUpgrade']))
                                            <a href="{{ route('services.show', $item->reference_type == 'App\Models\Service' ? $item->reference_id : $item->reference->service_id) }}" class="text-color-base font-bold hover:text-primary transition-colors hover:underline underline-offset-2">
                                                {{ $item->description }}
                                            </a>
                                        @else
                                            <span class="text-color-base font-bold">{{ $item->description }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-color-base font-medium">{{ $item->formattedPrice }}</td>
                                    <td class="px-6 py-5 text-color-base font-medium">{{ $item->quantity }}</td>
                                    <td class="px-6 py-5 text-right text-color-base font-bold">{{ $item->formattedTotal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($invoice->transactions->isNotEmpty())
            <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-sm overflow-hidden h-fit">
                <div class="px-6 py-5 border-b border-neutral/50 flex items-center gap-2">
                    <x-ri-exchange-dollar-line class="size-5 text-primary" />
                    <h3 class="font-bold text-color-base text-base">{{ __('invoices.transactions') }}</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-background/50 border-b border-neutral/30">
                                <th class="px-6 py-4 text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.date') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.transaction_id') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.gateway') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-color-muted uppercase tracking-wider">{{ __('invoices.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral/30">
                            @foreach ($invoice->transactions->sortByDesc('created_at') as $transaction)
                                <tr class="hover:bg-background/30 transition-colors">
                                    <td class="px-6 py-5 text-color-base font-medium">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-5 text-color-base font-medium font-mono text-sm">{{ $transaction->transaction_id }}</td>
                                    <td class="px-6 py-5 text-color-base font-medium">
                                        @if($transaction->is_credit_transaction)
                                            {{ __('invoices.paid_with_credits') }}
                                        @else
                                            {{ $transaction->gateway?->name ?? 'Manual' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-right text-color-base font-bold">{{ $transaction->formattedAmount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="lg:col-span-1 flex flex-col gap-6">

            <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-sm p-6 h-fit">
                <div class="flex items-center gap-2 mb-6">
                    <x-ri-information-fill class="size-5 text-primary" />
                    <h3 class="font-bold text-color-base text-base">Billing Information</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="text-xs font-bold text-color-muted uppercase tracking-wider mb-1">{{ __('invoices.issued_to') }}</div>
                        <div class="font-bold text-color-base text-base">{{ $invoice->user_name }}</div>
                        @foreach($invoice->user_properties as $property)
                            <div class="text-sm text-color-muted">{{ $property }}</div>
                        @endforeach
                    </div>

                    <div>
                        <div class="text-xs font-bold text-color-muted uppercase tracking-wider mb-1">{{ __('invoices.bill_to') }}</div>
                        <div class="text-sm text-color-base font-medium leading-relaxed">
                            {!! nl2br(e($invoice->bill_to)) !!}
                        </div>
                    </div>

                    @if($invoice->due_at)
                        <div>
                            <div class="text-xs font-bold text-color-muted uppercase tracking-wider mb-1">{{ __('invoices.due_date') }}</div>
                            <div class="text-sm font-bold text-color-base">{{ $invoice->due_at->format('d M Y') }}</div>
                        </div>
                    @endif
                    
                    @if($invoice->note ?? false)
                        <div>
                            <div class="text-xs font-bold text-color-muted uppercase tracking-wider mb-1">Notes</div>
                            <div class="text-sm text-color-base">{{ $invoice->note }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-sm p-6 h-fit">
                <div class="flex items-center gap-2 mb-6">
                    <x-ri-wallet-3-fill class="size-5 text-primary" />
                    <h3 class="font-bold text-color-base text-base">Payment Summary</h3>
                </div>

                <div class="space-y-4 mb-6">
                    @if ($invoice->formattedTotal->tax > 0)
                        <div class="flex justify-between items-center pb-4 border-b border-neutral/30">
                            <span class="text-color-muted text-sm font-medium">{{ __('invoices.subtotal') }}</span>
                            <span class="font-bold text-color-base">{{ $invoice->formattedTotal->format($invoice->formattedTotal->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-neutral/30">
                            <span class="text-color-muted text-sm font-medium">{{ $invoice->tax->name }} ({{ $invoice->tax->rate }}%)</span>
                            <span class="font-bold text-color-base">{{ $invoice->formattedTotal->formatted->tax }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-color-base font-bold text-lg">{{ __('invoices.total') }}</span>
                        <span class="font-black text-primary text-2xl">{{ $invoice->formattedTotal }}</span>
                    </div>
                    @if ($invoice->status == 'pending')
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-color-muted text-sm font-medium">Amount Due</span>
                            <span class="font-black text-warning text-xl">{{ $invoice->formattedRemaining }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col gap-3">
                    @if ($invoice->status == 'pending')
                        <button wire:click="$set('showPayModal', true)" wire:loading.attr="disabled" class="w-full flex items-center justify-center gap-2 p-3 bg-primary text-white font-bold rounded-xl shadow-sm hover:bg-primary/90 hover:-translate-y-0.5 transition-all">
                            <span wire:loading wire:target="$set('showPayModal')">
                                <x-ri-loader-5-fill class="size-5 animate-spin" />
                            </span>
                            <x-ri-bank-card-line class="size-5" wire:loading.remove wire:target="$set('showPayModal')" />
                            <span wire:loading.remove wire:target="$set('showPayModal')">{{ __('product.checkout') }}</span>
                        </button>

                        @if ($checkPayment || $invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Processing)->count() > 0)
                            <button wire:click="checkPaymentStatus" wire:loading.attr="disabled" class="w-full flex items-center justify-center gap-2 p-3 bg-background border border-neutral/50 text-color-base font-bold rounded-xl hover:bg-background-secondary transition-all">
                                <x-ri-refresh-line class="size-5" wire:loading.remove wire:target="checkPaymentStatus" />
                                <x-ri-loader-5-fill class="size-5 animate-spin" wire:loading wire:target="checkPaymentStatus" />
                                <span>Check Status</span>
                            </button>
                        @endif
                    @endif

                    <button wire:click="downloadPDF" wire:loading.attr="disabled" class="w-full flex items-center justify-center gap-2 p-3 bg-background border border-neutral/50 text-color-base font-bold rounded-xl hover:bg-background-secondary transition-all">
                        <span wire:loading wire:target="downloadPDF">
                            <x-ri-loader-5-fill class="size-5 animate-spin" />
                        </span>
                        <x-ri-download-line class="size-5" wire:loading.remove wire:target="downloadPDF" />
                        <span wire:loading.remove wire:target="downloadPDF">{{ __('invoices.download_pdf') }}</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>