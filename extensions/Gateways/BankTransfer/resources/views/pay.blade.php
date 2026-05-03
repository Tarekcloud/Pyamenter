<div class="space-y-4">
    <div class="p-4 bg-background-secondary border border-neutral rounded-lg">
        <h3 class="text-lg font-semibold mb-2">{{ __('Bank Transfer') }}</h3>
        <p class="text-sm text-base/50">{{ __('Please transfer the amount using the bank details below.') }}</p>
        @isset($discountAmount)
            @if($discountAmount > 0)
                <div class="mt-2 text-xs text-primary">
                    {{ __('You save :percent% with bank transfer. The discount has been applied to the amount.', ['percent' => $discountPercentage]) }}
                </div>
            @endif
        @endisset
        <div class="mt-3 flex items-center justify-between p-3 bg-background-secondary border border-neutral rounded-lg">
            <div>
                <p class="text-sm text-base/50">{{ __('Amount') }}</p>
                <div class="flex items-center gap-3">
                    <p class="text-xl font-semibold">{{ number_format($total, 2) }} {{ $currency }}</p>
                    @isset($discountAmount)
                        @if($discountAmount > 0)
                            <div class="text-xs text-primary flex items-center gap-2">
                                <span>-{{ number_format($discountAmount, 2) }} {{ $currency }}</span>
                                <span class="text-base/50">({{ $discountPercentage }}%)</span>
                            </div>
                        @endif
                    @endisset
                </div>
            </div>
            <div>
                <p class="text-sm text-base/50">{{ __('Reference') }}</p>
                <div class="flex items-center gap-2">
                    <p class="font-mono text-sm bg-background px-2 py-1 rounded">{{ $reference }}</p>
                    <button type="button" class="p-1 rounded hover:bg-background transition" aria-label="{{ __('Copy') }}"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $reference }}'); copied = true; setTimeout(() => copied = false, 1500)">
                        <span x-show="!copied">
                            <x-ri-file-copy-2-line class="size-4 text-base/50" />
                        </span>
                        <span x-show="copied">
                            <x-ri-check-line class="size-4 text-primary" />
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @if($daysToPay)
            <p class="mt-2 text-sm text-base/50">{{ __('Deadline: Please pay within :days days.', ['days' => $daysToPay]) }}</p>
        @endif
    </div>

    <div class="p-4 bg-background-secondary border border-neutral rounded-lg">
        <h4 class="text-base font-semibold mb-3">{{ __('Bank Details') }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <p class="text-xs text-base/50">{{ __('Account Holder') }}</p>
                <div class="flex items-center gap-2">
                    <p class="font-medium">{{ $accountHolder }}</p>
                    <button type="button" class="p-1 rounded hover:bg-background transition" aria-label="{{ __('Copy') }}"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $accountHolder }}'); copied = true; setTimeout(() => copied = false, 1500)">
                        <span x-show="!copied">
                            <x-ri-file-copy-2-line class="size-4 text-base/50" />
                        </span>
                        <span x-show="copied">
                            <x-ri-check-line class="size-4 text-primary" />
                        </span>
                    </button>
                </div>
            </div>
            <div>
                <p class="text-xs text-base/50">{{ __('Bank Name') }}</p>
                <p class="font-medium">{{ $bankName }}</p>
            </div>
            <div>
                <p class="text-xs text-base/50">{{ __('International Bank Account Number') }}</p>
                <div class="flex items-center gap-2">
                    <p class="font-mono">{{ $iban }}</p>
                    <button type="button" class="p-1 rounded hover:bg-background transition" aria-label="{{ __('Copy') }}"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $iban }}'); copied = true; setTimeout(() => copied = false, 1500)">
                        <span x-show="!copied">
                            <x-ri-file-copy-2-line class="size-4 text-base/50" />
                        </span>
                        <span x-show="copied">
                            <x-ri-check-line class="size-4 text-primary" />
                        </span>
                    </button>
                </div>
            </div>
            @if($bic)
            <div>
                <p class="text-xs text-base/50">{{ __('Bank Identifier Code') }}</p>
                <div class="flex items-center gap-2">
                    <p class="font-mono">{{ $bic }}</p>
                    <button type="button" class="p-1 rounded hover:bg-background transition" aria-label="{{ __('Copy') }}"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $bic }}'); copied = true; setTimeout(() => copied = false, 1500)">
                        <span x-show="!copied">
                            <x-ri-file-copy-2-line class="size-4 text-base/50" />
                        </span>
                        <span x-show="copied">
                            <x-ri-check-line class="size-4 text-primary" />
                        </span>
                    </button>
                </div>
            </div>
            @endif
            @if($bankAddress)
            <div class="md:col-span-2">
                <p class="text-xs text-base/50">{{ __('Bank Address') }}</p>
                <p>{{ nl2br(e($bankAddress)) }}</p>
            </div>
            @endif
        </div>
    </div>

    @if($instructions)
    <div class="p-4 bg-background-secondary border border-neutral rounded-lg">
        <h4 class="text-base font-semibold mb-2">{{ __('Instructions') }}</h4>
        <p class="text-sm">{{ nl2br(e($instructions)) }}</p>
    </div>
    @endif

    <div class="p-4 bg-background-secondary border border-neutral rounded-lg">
        <p class="text-sm text-base/50">{{ __('Once your transfer has been received, the invoice will be manually marked as paid.') }}</p>
    </div>
</div>