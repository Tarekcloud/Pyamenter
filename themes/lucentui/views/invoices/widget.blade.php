<div class="space-y-3">
    @forelse ($invoices->take(3) as $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate>
        <div class="group bg-gradient-to-br from-background-secondary/50 to-background-secondary/30 border border-neutral/50 p-4 rounded-lg transition-all hover:border-primary/50 hover:shadow-lg mb-2">

        <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-secondary/10 p-2 rounded-lg">
                        <x-ri-bill-line class="size-5 text-secondary" />
                    </div>
                    <div>
                        <p class="font-semibold text-base group-hover:text-primary transition-colors">
                            {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-base/80">{{ $invoice->formattedTotal }}</span>
                    <div class="size-5 rounded-full p-0.5
                        @if ($invoice->status == 'paid') text-success bg-success/20
                        @elseif($invoice->status == 'cancelled') text-info bg-info/20
                        @else text-warning bg-warning/20 @endif">
                        @if ($invoice->status == 'paid') <x-ri-checkbox-circle-fill />
                        @elseif($invoice->status == 'cancelled') <x-ri-forbid-fill />
                        @else <x-ri-error-warning-fill /> @endif
                    </div>
                </div>
            </div>

            <div class="pl-12 space-y-0.5">
                <p class="text-sm text-base/80 truncate" title="{{ $invoice->items->first()->description ?? 'General Invoice' }}">
                    {{ $invoice->items->first()->description ?? 'General Invoice' }}
                </p>
                <p class="text-xs text-base/60">
                    {{ __('invoices.invoice_date') }}: {{ $invoice->created_at->format('d M Y') }}
                </p>
            </div>
        </div>
    </a>
    @empty
        <div class="text-center py-8">
            <div class="flex justify-center mb-3">
                <x-ri-file-info-line class="size-8 text-base/40" />
            </div>
            <p class="text-base/60">No Invoices Found</p>
        </div>
    @endforelse
</div>