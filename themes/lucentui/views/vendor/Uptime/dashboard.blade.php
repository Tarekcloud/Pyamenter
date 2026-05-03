<div class="container mx-auto px-8 py-8 animate-fade-in-up" wire:init="loadData">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="bg-primary/10 text-primary p-3 rounded-full shadow-md">
                <x-ri-server-fill class="size-6" />
            </div>
            <div>
                <h1 class="text-3xl font-bold text-color-base">Server Status</h1>
                <p class="text-color-muted text-sm">
                    Real-time service health monitoring
                    @if($lastUpdated)
                        <span class="opacity-75">· Last updated: {{ $lastUpdated }}</span>
                    @endif
                </p>
            </div>
        </div>

        <button wire:click="loadData" 
                wire:loading.attr="disabled"
                class="group/btn inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
            
            <span wire:loading.remove wire:target="loadData" class="flex items-center gap-2">
                <x-ri-refresh-line class="size-4 transform transition-transform duration-500 group-hover/btn:rotate-180" />
                <span>Refresh</span>
            </span>
            
            <span wire:loading wire:target="loadData" class="flex items-center gap-2">
                <x-ri-loader-4-line class="size-4 animate-spin" />
                <span>Checking...</span>
            </span>
        </button>
    </div>

    @if($error)
        <div class="mb-6 rounded-2xl bg-red-500/10 border border-red-500/20 p-4 animate-fade-in-up">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-ri-error-warning-fill class="h-5 w-5 text-red-500" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-600 dark:text-red-400">Connection Error</h3>
                    <div class="mt-1 text-sm text-red-600/80 dark:text-red-400/80">
                        {{ $error }}
                        @if(str_contains($error, '404'))
                            <p class="mt-1 font-semibold">Check your Status Page Slug configuration.</p>
                        @elseif(str_contains($error, '401'))
                            <p class="mt-1 font-semibold">Check your API credentials.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($loading && !$statusData)
        <div class="flex flex-col items-center justify-center py-24 text-color-muted animate-pulse">
            <x-ri-radar-line class="size-12 animate-spin mb-4 text-primary" />
            <p>Scanning services...</p>
        </div>
    @endif

    @if($statusData && isset($statusData['publicGroupList']))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($statusData['publicGroupList'] as $group)
                @foreach($group['monitorList'] as $index => $monitor)
                    @php
                        $status = $monitor['status'] ?? 'unknown';
                        $uptimePercentage = $monitor['uptime'] ?? null;
                        
                        // Color & Icon Logic
                        $colorClass = match($status) {
                            'up' => 'text-green-500 bg-green-500',
                            'down' => 'text-red-500 bg-red-500',
                            'pending' => 'text-yellow-500 bg-yellow-500',
                            'maintenance' => 'text-blue-500 bg-blue-500',
                            default => 'text-gray-500 bg-gray-500',
                        };

                        $textClass = match($status) {
                            'up' => 'text-green-600 dark:text-green-400',
                            'down' => 'text-red-600 dark:text-red-400',
                            'pending' => 'text-yellow-600 dark:text-yellow-400',
                            'maintenance' => 'text-blue-600 dark:text-blue-400',
                            default => 'text-gray-500 dark:text-gray-400',
                        };

                        $statusLabel = ucfirst($status);
                    @endphp

                    <div class="group bg-gradient-to-br from-background-secondary/50 to-background-secondary/30 border border-neutral/50 rounded-2xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-[1.02] overflow-hidden animate-fade-in-up" 
                         style="animation-delay: {{ $index * 0.05 }}s;">
                        
                        <div class="p-6 pb-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-2.5 rounded-full animate-pulse {{ $colorClass }}"></div>
                                    <span class="text-xs font-bold uppercase tracking-wide {{ $textClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <div class="text-color-muted text-xs bg-neutral/10 px-2 py-1 rounded-md">
                                    {{ $group['name'] }}
                                </div>
                            </div>
                            
                            <h2 class="text-xl font-bold text-color-base mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-1">
                                {{ $monitor['name'] }}
                            </h2>
                            
                            <div class="space-y-2 mt-4">
                                @if($uptimePercentage)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-color-muted flex items-center gap-1">
                                        <x-ri-bar-chart-box-line class="size-4" /> Uptime (30d)
                                    </span>
                                    <span class="font-mono font-medium text-color-base">{{ number_format($uptimePercentage, 2) }}%</span>
                                </div>
                                @endif

                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-color-muted flex items-center gap-1">
                                        <x-ri-global-line class="size-4" /> Type
                                    </span>
                                    <span class="text-color-base">{{ $monitor['type'] ?? 'HTTP' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-neutral/10 bg-neutral/5">
                            <div class="flex items-center justify-between text-xs text-color-muted">
                                <div class="flex items-center gap-1.5" title="Last Incident">
                                    <x-ri-history-line class="size-3.5" />
                                    <span>
                                        @if(isset($monitor['lastEvent']) && $monitor['lastEvent']['type'] === 'down')
                                            {{ \Carbon\Carbon::parse($monitor['lastEvent']['datetime'])->diffForHumans() }}
                                        @else
                                            No recent incidents
                                        @endif
                                    </span>
                                </div>
                                <span class="font-mono opacity-50">ID: {{ $monitor['id'] }}</span>
                            </div>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-primary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @elseif(!$loading && !$error)
        <div class="text-center py-24 rounded-xl bg-background-secondary/30 border border-neutral/50 border-dashed animate-fade-in-up">
            <div class="bg-neutral/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-ri-cloud-off-line class="size-8 text-color-muted" />
            </div>
            <h3 class="text-lg font-bold text-color-base">No Monitoring Data</h3>
            <p class="text-color-muted text-sm mt-1 max-w-sm mx-auto">
                Status page configuration exists but no monitors were returned from the API.
            </p>
            <button wire:click="loadData" class="mt-6 text-primary hover:text-primary/80 text-sm font-medium underline">
                Try Refreshing Again
            </button>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {
        const interval = {{ $refreshInterval * 1000 }};
        if (interval > 0) {
            setInterval(() => {
                Livewire.emit('refreshData');
            }, interval);
        }
    });
</script>
@endpush