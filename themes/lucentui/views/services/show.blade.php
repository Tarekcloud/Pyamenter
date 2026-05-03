<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    
    @if($invoice = $service->invoices()->where('status', 'pending')->first())
        <div class="w-full mb-6">
            <div class="bg-gradient-to-r from-warning/10 to-transparent border border-warning/20 rounded-xl p-4 backdrop-blur-sm flex items-start gap-3">
                <x-ri-error-warning-fill class="size-5 text-warning mt-0.5 shrink-0" />
                <p class="font-medium text-warning text-sm">
                    {{ __('services.outstanding_invoice') }}
                    <a href="{{ route('invoices.show', $invoice)}}" class="underline hover:text-warning/80 underline-offset-2 font-bold decoration-warning/50 hover:decoration-warning">{{ __('services.view_and_pay') }}</a>.
                </p>
            </div>
        </div>
    @endif

    @if($service->status == 'suspended')
        <div class="w-full mb-6">
            <div class="bg-gradient-to-r from-red-500/10 to-transparent border border-red-500/20 rounded-xl p-4 backdrop-blur-sm flex items-start gap-3">
                <x-ri-prohibited-line class="size-5 text-red-500 mt-0.5 shrink-0" />
                <p class="font-medium text-red-500 text-sm">
                    This service has been suspended. Please open a
                    <a href="{{ route('tickets.create') }}" class="underline hover:text-red-600 underline-offset-2 font-bold decoration-red-500/50 hover:decoration-red-600">support ticket</a>
                    to resolve this matter.
                </p>
            </div>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center justify-center p-2 rounded-lg bg-primary/10 text-primary">
                    <x-ri-server-fill class="size-6" />
                </span>
                <span class="text-primary text-sm font-medium">#{{ $service->id }}</span>
            </div>
            <h1 class="text-3xl font-bold text-color-base leading-tight">
                {{ __('services.services') }}
            </h1>
        </div>

        <div>
            @php
                $statusColor = match($service->status) {
                    'active' => 'success',
                    'cancelled' => 'neutral',
                    'suspended' => 'warning',
                    default => 'warning'
                };
                
                if($service->cancellation && $service->status == 'active') {
                    $statusLabel = __('services.statuses.cancellation_pending');
                    $statusColor = 'warning';
                    $statusIcon = 'ri-time-line';
                } else {
                    $statusLabel = __('services.statuses.' . $service->status);
                    $statusIcon = match($service->status) {
                        'active' => 'ri-checkbox-circle-fill',
                        'cancelled' => 'ri-close-circle-fill',
                        'suspended' => 'ri-prohibited-fill',
                        default => 'ri-error-warning-fill'
                    };
                }
            @endphp

            <div class="flex items-center gap-2 px-4 py-2 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl">
                <x-dynamic-component :component="$statusIcon" class="size-5 text-{{ $statusColor }}" />
                <span class="text-{{ $statusColor }} font-bold text-sm uppercase tracking-wide">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-sm overflow-hidden h-fit">
            <div class="px-6 py-5 border-b border-neutral/50 flex items-center gap-2">
                <x-ri-file-list-3-line class="size-5 text-primary" />
                <h3 class="font-bold text-color-base text-base">{{ __('services.product_details') }}</h3>
            </div>
            
            <div class="p-0">
                <div class="grid grid-cols-1 divide-y divide-neutral/30">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-5 hover:bg-background/30 transition-colors">
                        <span class="text-color-muted text-sm font-medium mb-1 sm:mb-0">{{ __('services.name') }}</span>
                        <span class="font-bold text-color-base text-right">{{ $service->product->name }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-5 hover:bg-background/30 transition-colors">
                        <span class="text-color-muted text-sm font-medium mb-1 sm:mb-0">{{ __('services.price') }}</span>
                        <span class="font-bold text-success text-lg text-right">{{ $service->formattedPrice }}</span>
                    </div>

                    @if($service->plan->type == 'recurring')
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-5 hover:bg-background/30 transition-colors">
                            <span class="text-color-muted text-sm font-medium mb-1 sm:mb-0">{{ __('services.billing_cycle') }}</span>
                            <span class="font-bold text-color-base text-right">
                                {{ __('services.every_period', [
                                    'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                                    'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
                                ]) }}
                            </span>
                        </div>
                    @endif

                    @if($service->expires_at)
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-5 hover:bg-background/30 transition-colors">
                            <span class="text-color-muted text-sm font-medium mb-1 sm:mb-0">{{ __('services.expires_at') }}</span>
                            <span class="font-bold text-color-base text-right">{{ $service->expires_at->format('d M Y') }}</span>
                        </div>
                    @endif

                    @include('services.partials.billing-agreement')

                    @foreach ($fields as $field)
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-5 hover:bg-background/30 transition-colors">
                            <span class="text-color-muted text-sm font-medium mb-1 sm:mb-0">{{ $field['label'] }}</span>
                            <span class="font-bold text-color-base text-right">{{ $field['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($service->cancellable || $service->upgradable || count($buttons) > 0)
            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-sm p-6 h-fit">
                    <div class="flex items-center gap-2 mb-6">
                        <x-ri-flashlight-fill class="size-5 text-warning" />
                        <h3 class="font-bold text-color-base text-base">{{ __('services.actions') }}</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        
                        @if($service->upgradable)
                            <a href="{{ route('services.upgrade', $service->id) }}" class="col-span-2 group">
                                <button class="w-full flex items-center justify-between p-4 bg-gradient-to-br from-primary to-primary/80 text-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-start">
                                        <span class="font-bold text-sm">{{ __('services.upgrade') }}</span>
                                        <span class="text-xs text-white/80">Change plan</span>
                                    </div>
                                    <x-ri-arrow-up-circle-fill class="size-8 text-white/90 group-hover:text-white" />
                                </button>
                            </a>
                        @endif

                        @if($service->upgrade()->where('status', 'pending')->exists())
                             <button class="col-span-2 w-full flex items-center justify-between p-4 bg-gradient-to-br from-warning to-warning/80 text-white rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-not-allowed opacity-80"
                                @click="Alpine.store('notifications').addNotification([{message: '{{ __('services.upgrade_pending') }}', type: 'error'}])">
                                <div class="flex flex-col items-start">
                                    <span class="font-bold text-sm">{{ __('services.upgrade') }}</span>
                                    <span class="text-xs text-white/80">Pending...</span>
                                </div>
                                <x-ri-loader-4-line class="size-6 animate-spin text-white/90" />
                            </button>
                        @endif

                        @foreach ($buttons as $button)
                            @php
                                $isLink = !isset($button['function']);
                                $action = $isLink ? '' : "wire:click=\"goto('{$button['function']}')\"";
                                $href = $isLink ? "href=\"{$button['url']}\"" : '';
                                $tag = $isLink ? 'a' : 'button';
                            @endphp

                            <{{ $tag }} {!! $href !!} {!! $action !!} class="col-span-1 group {{ $isLink ? 'block h-full' : 'h-full w-full' }}">
                                <div class="h-full flex flex-col items-center justify-center gap-2 p-4 bg-background border border-neutral/30 rounded-xl transition-all duration-200 hover:border-primary/50 hover:bg-background-secondary hover:-translate-y-0.5 cursor-pointer">
                                    <x-ri-link-m class="size-5 text-color-muted group-hover:text-primary transition-colors" />
                                    <span class="text-xs font-bold text-color-base text-center line-clamp-1">{{ $button['label'] }}</span>
                                </div>
                            </{{ $tag }}>
                        @endforeach

                        @if($service->cancellable)
                            <button wire:click="$set('showCancel', true)" class="col-span-2 group mt-2">
                                <div class="w-full flex items-center justify-center gap-2 p-3 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl transition-all duration-200 hover:bg-red-500 hover:text-white hover:border-red-500 hover:shadow-md">
                                    <x-ri-close-circle-line class="size-5" />
                                    <span class="text-sm font-bold" wire:loading.remove wire:target="$set('showCancel', true)">{{ __('services.cancel') }} Service</span>
                                    <x-loading target="$set('showCancel', true)" class="size-4" />
                                </div>
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($showCancel)
        <x-modal open="true" title="{{ __('services.cancellation', ['service' => $service->product->name]) }}" width="max-w-xl">
            <livewire:services.cancel :service="$service" />
            <x-slot name="closeTrigger">
                 <button wire:click="$set('showCancel', false)" class="p-2 rounded-lg hover:bg-neutral/10 text-color-muted transition-colors">
                    <x-ri-close-line class="size-6" />
                </button>
            </x-slot>
        </x-modal>
    @endif

    @if (count($views) > 0)
        <div class="mt-8 relative">
            @if (count($views) > 1)
                <div class="flex w-full mb-6 border-b border-neutral/50 overflow-x-auto scrollbar-hide gap-6"> 
                    @foreach ($views as $view)
                        <button wire:click="changeView('{{ $view['name'] }}')"
                            class="pb-3 text-sm font-bold uppercase tracking-wide transition-all duration-200 whitespace-nowrap border-b-2 
                            {{ $view['name'] == $currentView ? 'border-primary text-primary' : 'border-transparent text-color-muted hover:text-color-base' }}">
                            {{ $view['label'] }}
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="relative min-h-[200px]">
                <div wire:loading wire:target="changeView" class="absolute inset-0 flex items-center justify-center bg-background/50 backdrop-blur-sm z-20 rounded-xl">
                    <x-ri-loader-4-line class="size-8 animate-spin text-primary" />
                </div>
                
                <div>
                    {!! $extensionView !!}
                </div>
            </div>
        </div>
    @endif
</div>