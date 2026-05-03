<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb class="mb-6" />

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-4 mb-8">
        <h1 class="text-3xl lg:text-4xl font-bold text-color-base">
            {{ __('compliance-tools::client.navigation_privacy') }}
        </h1>
        
        <a
            href="{{ route('compliance.data-export') }}"
            download
            rel="noopener"
            class="inline-flex items-center justify-center px-5 py-2.5 border border-neutral/50 bg-background/50 text-color-base text-sm font-medium rounded-xl hover:bg-background-secondary hover:text-primary hover:border-primary/50 transition-all duration-200 shadow-sm"
        >
            <x-ri-download-cloud-2-line class="size-5 mr-2" />
            {{ __('compliance-tools::client.export_data') }}
        </a>
    </div>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8">
        <div class="flex items-center mb-6">
            <div class="bg-red-500/10 p-3 rounded-full flex-shrink-0 shadow-sm mr-4">
                <x-ri-delete-bin-line class="size-6 text-red-500" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('compliance-tools::client.deletion_heading') }}</h2>
                <p class="text-sm text-color-muted">{{ __('compliance-tools::client.deletion_description') }}</p>
            </div>
        </div>

        @php
            $deletionStatus = __('compliance-tools::client.deletion_status_none');
            $statusColor = 'neutral';
            
            if ($deletionRequest) {
                switch ($deletionRequest->status) {
                    case \Paymenter\Extensions\Others\ComplianceTools\Models\AccountDeletionRequest::STATUS_PENDING:
                        $deletionStatus = __('compliance-tools::client.deletion_status_pending');
                        $statusColor = 'warning';
                        break;
                    case \Paymenter\Extensions\Others\ComplianceTools\Models\AccountDeletionRequest::STATUS_COMPLETED:
                        $deletionStatus = __('compliance-tools::client.deletion_status_completed', ['date' => optional($deletionRequest->processed_at)->toDayDateTimeString()]);
                        $statusColor = 'success';
                        break;
                    case \Paymenter\Extensions\Others\ComplianceTools\Models\AccountDeletionRequest::STATUS_DENIED:
                        $deletionStatus = __('compliance-tools::client.deletion_status_denied');
                        $statusColor = 'red'; // using red instead of danger to match your styling
                        break;
                }
            }

            $disableDeletion = $deletionRequest && in_array($deletionRequest->status, [
                \Paymenter\Extensions\Others\ComplianceTools\Models\AccountDeletionRequest::STATUS_PENDING,
                \Paymenter\Extensions\Others\ComplianceTools\Models\AccountDeletionRequest::STATUS_COMPLETED,
            ]);
        @endphp

        <div class="bg-{{ $statusColor == 'neutral' ? 'background' : $statusColor }}/10 border border-{{ $statusColor == 'neutral' ? 'neutral' : $statusColor }}/20 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <x-ri-information-line class="size-5 text-{{ $statusColor == 'neutral' ? 'color-muted' : ($statusColor == 'red' ? 'red-500' : $statusColor) }} mr-3" />
                <span class="text-sm font-medium text-{{ $statusColor == 'neutral' ? 'color-base' : ($statusColor == 'red' ? 'red-600' : $statusColor) }}">
                    {{ $deletionStatus }}
                </span>
            </div>
        </div>

        <form wire:submit.prevent="submitDeletionRequest" onsubmit="return confirm('{{ addslashes(__('compliance-tools::client.deletion_confirm')) }}');">
            <div class="space-y-6">
                <x-form.textarea
                    name="reason"
                    wire:model.defer="reason"
                    :label="__('compliance-tools::client.deletion_reason_label')"
                    divClass="!mt-0"
                    :placeholder="__('compliance-tools::client.deletion_reason_label')"
                    rows="3"
                    class="bg-background/50 border-neutral/50 focus:ring-red-500 focus:border-red-500 rounded-xl"
                />

                <x-form.input
                    name="password"
                    type="password"
                    wire:model.defer="password"
                    :label="__('compliance-tools::client.deletion_password_label')"
                    autocomplete="current-password"
                    class="bg-background/50 border-neutral/50 focus:ring-red-500 focus:border-red-500 rounded-xl"
                />

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="$disableDeletion"
                        wire:target="submitDeletionRequest"
                        wire:loading.attr="disabled"
                    >
                        <x-ri-user-unfollow-line class="size-4 mr-2" />
                        {{ __('compliance-tools::client.deletion_submit') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg" style="animation-duration: 0.8s;">
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-full flex-shrink-0 shadow-sm mr-4">
                <x-ri-customer-service-2-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('compliance-tools::client.pin_heading') }}</h2>
                <p class="text-sm text-color-muted">{{ __('compliance-tools::client.pin_description') }}</p>
            </div>
        </div>

        @php
            $hasGeneratedPin = filled($generatedPin);
            $maskedPin = $hasGeneratedPin ? str_repeat('•', strlen($generatedPin)) : '';
            $plainPin = $generatedPin ?? '';
            $disableRevoke = !$activePin;
        @endphp

        @if($hasGeneratedPin)
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 mb-6 text-center"
                x-data="{
                    pin: @js($plainPin),
                    async copy() {
                        if (!this.pin) return;
                        try {
                            await navigator.clipboard.writeText(this.pin);
                        } catch (e) {
                            const helper = document.createElement('textarea');
                            helper.value = this.pin;
                            helper.style.position = 'fixed';
                            helper.style.opacity = '0';
                            document.body.appendChild(helper);
                            helper.select();
                            document.execCommand('copy');
                            document.body.removeChild(helper);
                        }
                        $wire.pinCopied(); // Assuming this method exists or you can remove if distinct toast handles it
                    }
                }">
                
                <p class="text-sm font-semibold text-primary flex items-center justify-center gap-2 mb-4">
                    <x-ri-key-2-line class="size-5" />
                    {{ __('compliance-tools::client.pin_plaintext_notice') }}
                </p>
                
                <div class="bg-background/80 border border-neutral/50 rounded-xl p-4 mb-6 inline-block min-w-[200px]">
                    <p class="font-mono text-3xl tracking-[0.5em] text-primary select-all font-bold">
                        {{ $showGeneratedPin ? $generatedPin : $maskedPin }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-3">
                    <button type="button" wire:click="toggleGeneratedPinVisibility" 
                        class="inline-flex items-center px-4 py-2 border border-neutral/50 text-sm font-medium rounded-xl shadow-sm text-color-base bg-background/50 hover:bg-background-secondary transition-colors duration-200">
                        @if($showGeneratedPin)
                            <x-ri-eye-off-line class="size-4 mr-2" />
                            {{ __('compliance-tools::client.pin_hide') }}
                        @else
                            <x-ri-eye-line class="size-4 mr-2" />
                            {{ __('compliance-tools::client.pin_show') }}
                        @endif
                    </button>

                    <button type="button" x-on:click="copy()"
                        class="inline-flex items-center px-4 py-2 border border-neutral/50 text-sm font-medium rounded-xl shadow-sm text-color-base bg-background/50 hover:bg-background-secondary transition-colors duration-200">
                        <x-ri-file-copy-line class="size-4 mr-2" />
                        {{ __('compliance-tools::client.pin_copy') }}
                    </button>
                </div>
            </div>
        @elseif($activePin)
            <div class="bg-success/10 border border-success/20 rounded-xl p-4 mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-success/20 p-2 rounded-full mr-3">
                        <x-ri-shield-check-line class="size-5 text-success" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-success">Active Support PIN</p>
                        <p class="text-xs text-color-muted">{{ __('compliance-tools::client.pin_active_until', ['date' => optional($activePin->expires_at)->toDayDateTimeString()]) }}</p>
                    </div>
                </div>
                <div class="bg-success/10 border border-success/20 rounded-full px-3 py-1">
                     <span class="text-xs font-bold text-success uppercase">Active</span>
                </div>
            </div>
        @else
            <div class="bg-warning/5 border border-warning/20 rounded-xl p-6 mb-6 text-center">
                <div class="flex flex-col items-center justify-center">
                    <x-ri-error-warning-line class="size-8 text-warning mb-2" />
                    <p class="text-sm font-medium text-color-base">{{ __('compliance-tools::client.pin_none') }}</p>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4 mt-6">
            <button
                type="button"
                wire:click="generatePin"
                wire:target="generatePin"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex-1 sm:flex-none"
            >
                <x-ri-refresh-line class="size-5 mr-2" />
                {{ __('compliance-tools::client.pin_generate') }}
            </button>

            <button
                type="button"
                wire:click="revokePin"
                wire:target="revokePin"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center px-6 py-3 border border-red-500/50 text-base font-medium rounded-xl shadow-sm text-red-700 bg-red-500/10 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex-1 sm:flex-none"
                :disabled="$disableRevoke"
            >
                <x-ri-close-circle-line class="size-5 mr-2" />
                {{ __('compliance-tools::client.pin_revoke') }}
            </button>
        </div>
    </div>
</div>