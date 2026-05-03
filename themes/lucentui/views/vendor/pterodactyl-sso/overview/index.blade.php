@php
    $statusMeta = $statusMeta ?? ['label' => 'Status', 'classes' => ''];
    $generatedAt = $generated_at ?? now()->toDayDateTimeString();

    $serverName = $server['name'] ?? 'Server';
    $serverIdentifier = $server['identifier'] ?? ($server['id'] ?? '—');
    $uptimeLabel = $server['uptime_label'] ?? '—';

    $primaryIp = $primaryAllocationDisplay['display'] ?? ($metadata['panelUrl'] ?? '—');

    $cpuPercent = isset($usage['cpu']['percent']) ? number_format((float) $usage['cpu']['percent'], 2) . '%' : '—';
    $cpuLimitLabel = $usage['cpu']['limit_label'] ?? 'Unlimited';

    $memoryUsedBytes = $usage['memory']['used_bytes'] ?? null;
    $memoryLimitLabel = $usage['memory']['limit_label'] ?? 'Unlimited';
    $diskUsedBytes = $usage['disk']['used_bytes'] ?? null;
    $diskLimitLabel = $usage['disk']['limit_label'] ?? 'Unlimited';

    $memoryUsedLabel = $memoryUsedBytes !== null ? number_format($memoryUsedBytes / (1024 ** 3), 2) . ' GiB' : '—';
    $diskUsedLabel = $diskUsedBytes !== null ? number_format($diskUsedBytes / (1024 ** 3), 2) . ' GiB' : '—';

    $networkOut = $networkStats['tx_label'] ?? '—';
    $networkIn = $networkStats['rx_label'] ?? '—';

    $limitsRow = [
        'Memory' => $limits['memory']['label'] ?? 'Unknown',
        'Disk' => $limits['disk']['label'] ?? 'Unknown',
        'CPU' => $limits['cpu']['label'] ?? 'Unknown',
        'Backups' => $featureLimitsFormatted['Backups'] ?? '—',
        'Allocation' => $featureLimitsFormatted['Allocations'] ?? '—',
        'Database' => $featureLimitsFormatted['Databases'] ?? '—',
    ];

    $limitIcons = [
        'Memory' => 'ram-2-line',
        'Disk' => 'hard-drive-2-line',
        'CPU' => 'cpu-line',
        'Backups' => 'archive-line',
        'Allocation' => 'server-line',
        'Database' => 'database-2-line',
    ];

    $refreshInterval = $refreshInterval ?? 30;
    $serviceId = $server['service_id'] ?? null;
@endphp

<div
    id="pterodactyl-overview"
    class="mx-auto"
    data-refresh-interval="{{ $refreshInterval }}"
    @if($serviceId)
        data-refresh-endpoint="{{ route('services.pterodactyl.metrics', ['service' => $serviceId]) }}"
        data-power-endpoint="{{ route('services.pterodactyl.power', ['service' => $serviceId]) }}"
    @endif
>
    <x-navigation.breadcrumb class="mb-6" />

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-4 mb-8">
        <div>
            <h1 class="text-3xl lg:text-4xl font-bold text-color-base">Server Overview</h1>
            <p class="text-sm text-color-muted mt-1">Generated {{ $generatedAt }}</p>
        </div>
        
        <div class="flex items-center">
            <span
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-base font-semibold border border-neutral/20 bg-background-secondary/50"
                data-status-badge
                data-status-base-class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-base font-semibold border"
                data-status-classes='{
                    "running": "text-success border-success/30 bg-success/10",
                    "starting": "text-yellow-600 border-yellow-500/30 bg-yellow-500/10",
                    "stopping": "text-orange-600 border-orange-500/30 bg-orange-500/10",
                    "offline": "text-error border-error/30 bg-error/10",
                    "default": "text-color-base border-neutral/20 bg-background-secondary/50"
                }'
            >
                <x-ri-checkbox-circle-fill class="size-4" />
                <span data-status-label>{{ ucfirst(strtolower($statusMeta['label'] ?? 'Status')) }}</span>
            </span>
        </div>
    </div>

    @if(!empty($error))
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-6 py-4 text-sm text-red-500 mb-6 flex items-center gap-3">
            <x-ri-error-warning-fill class="size-5 flex-shrink-0" />
            {{ $error }}
        </div>
    @endif

    @if(!empty($warnings))
        <div class="rounded-xl border border-warning/30 bg-warning/10 px-6 py-4 text-sm text-warning mb-6">
            <div class="flex items-start gap-3">
                <x-ri-alert-fill class="size-5 flex-shrink-0 mt-0.5" />
                <ul class="list-disc space-y-1 pl-4">
                    @foreach($warnings as $warn)
                        <li>{{ $warn }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8">
        <div class="flex flex-col xl:flex-row gap-8 items-start xl:items-center">
            
            <div class="flex-1 space-y-4 w-full">
                <div class="flex flex-col gap-1">
                    <h2 class="text-2xl font-bold text-color-base flex items-center gap-3">
                        <x-ri-server-fill class="size-6 text-primary" />
                        {{ $serverName }}
                    </h2>
                    <p class="text-color-muted text-sm ml-9">{{ $primaryIp }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2">
                    <div class="bg-background/50 border border-neutral/50 rounded-xl p-3 flex items-center gap-3">
                        <div class="bg-neutral/10 p-2 rounded-full text-color-muted">
                            <x-ri-hashtag class="size-4" />
                        </div>
                        <div>
                            <p class="text-xs text-color-muted uppercase font-bold">Server ID</p>
                            <p class="text-sm font-medium text-color-base">{{ $serverIdentifier }}</p>
                        </div>
                    </div>

                    <div class="bg-background/50 border border-neutral/50 rounded-xl p-3 flex items-center gap-3">
                        <div class="bg-neutral/10 p-2 rounded-full text-color-muted">
                            <x-ri-time-line class="size-4" />
                        </div>
                        <div>
                            <p class="text-xs text-color-muted uppercase font-bold">Uptime</p>
                            <p class="text-sm font-medium text-color-base" data-uptime>{{ $uptimeLabel }}</p>
                        </div>
                    </div>

                    <div 
                        class="bg-background/50 border border-neutral/50 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-primary/50 transition-colors group"
                        data-copy-target
                        data-copy-value="{{ $primaryIp }}"
                        title="Click to copy IP"
                    >
                        <div class="bg-primary/10 p-2 rounded-full text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                            <x-ri-global-line class="size-4" />
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs text-color-muted uppercase font-bold">IP Address</p>
                            <p class="text-sm font-medium text-color-base truncate" data-copy-label>{{ $primaryIp }}</p>
                        </div>
                        <x-ri-file-copy-line class="size-4 text-color-muted ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full xl:w-auto">
                <div class="relative group">
                    <button
                        type="button"
                        class="group/btn flex items-center justify-center size-12 rounded-xl text-white shadow-sm transition-all active:scale-95 bg-success hover:bg-success/90 disabled:opacity-70 disabled:cursor-not-allowed"
                        data-power-signal="start"
                    >
                        <x-ri-play-fill class="size-6 group-disabled/btn:hidden" />
                        <x-ri-loader-4-line class="size-6 animate-spin hidden group-disabled/btn:block" />
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-neutral-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                        Start Server
                    </div>
                </div>

                <div class="relative group">
                    <button
                        type="button"
                        class="group/btn flex items-center justify-center size-12 rounded-xl text-white shadow-sm transition-all active:scale-95 bg-warning hover:bg-warning/90 disabled:opacity-70 disabled:cursor-not-allowed"
                        data-power-signal="restart"
                    >
                        <x-ri-restart-line class="size-6 group-disabled/btn:hidden" />
                        <x-ri-loader-4-line class="size-6 animate-spin hidden group-disabled/btn:block" />
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-neutral-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                        Restart Server
                    </div>
                </div>

                <div class="relative group">
                    <button
                        type="button"
                        class="group/btn flex items-center justify-center size-12 rounded-xl text-white shadow-sm transition-all active:scale-95 bg-red-500 hover:bg-red-600 disabled:opacity-70 disabled:cursor-not-allowed"
                        data-power-signal="stop"
                    >
                        <x-ri-stop-fill class="size-6 group-disabled/btn:hidden" />
                        <x-ri-loader-4-line class="size-6 animate-spin hidden group-disabled/btn:block" />
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-neutral-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                        Stop Server
                    </div>
                </div>

                <div class="relative group">
                    <button
                        type="button"
                        class="group/btn flex items-center justify-center size-12 rounded-xl text-white shadow-sm transition-all active:scale-95 bg-neutral-600 hover:bg-neutral-700 disabled:opacity-70 disabled:cursor-not-allowed"
                        data-power-signal="kill"
                        onclick="return confirm('Are you sure you want to forcibly kill the server? This may cause data corruption.')"
                    >
                        <x-ri-skull-line class="size-6 group-disabled/btn:hidden" />
                        <x-ri-loader-4-line class="size-6 animate-spin hidden group-disabled/btn:block" />
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-neutral-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                        Kill Server
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 mt-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-5 shadow-sm" data-metric-card="cpu">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-wide text-color-muted">CPU Load</p>
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <x-ri-cpu-line class="size-5" />
                </div>
            </div>
            <p class="text-2xl font-bold text-color-base flex items-baseline gap-1">
                <span data-cpu-used>{{ $cpuPercent }}</span>
            </p>
            <p class="text-xs text-color-muted mt-1">Limit: <span data-cpu-limit>{{ $cpuLimitLabel }}</span></p>
        </div>

        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-5 shadow-sm" data-metric-card="memory" data-limit-bytes="{{ $usageBars['memory']['limit_bytes'] ?? 0 }}">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-wide text-color-muted">Memory</p>
                <div class="bg-blue-500/10 p-2 rounded-lg text-blue-500">
                    <x-ri-ram-2-line class="size-5" />
                </div>
            </div>
            <p class="text-2xl font-bold text-color-base">
                <span data-memory-used>{{ $memoryUsedLabel }}</span>
            </p>
            <p class="text-xs text-color-muted mt-1">Limit: <span data-memory-limit>{{ $memoryLimitLabel }}</span></p>
        </div>

        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-5 shadow-sm" data-metric-card="disk" data-limit-bytes="{{ $usageBars['disk']['limit_bytes'] ?? 0 }}">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-wide text-color-muted">Disk Space</p>
                <div class="bg-orange-500/10 p-2 rounded-lg text-orange-500">
                    <x-ri-hard-drive-2-line class="size-5" />
                </div>
            </div>
            <p class="text-2xl font-bold text-color-base">
                <span data-disk-used>{{ $diskUsedLabel }}</span>
            </p>
            <p class="text-xs text-color-muted mt-1">Limit: <span data-disk-limit>{{ $diskLimitLabel }}</span></p>
        </div>

        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-wide text-color-muted">Network</p>
                <div class="bg-purple-500/10 p-2 rounded-lg text-purple-500">
                    <x-ri-router-line class="size-5" />
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-color-muted flex items-center gap-1"><x-ri-upload-2-line class="size-3" /> Out</span>
                    <span class="text-sm font-semibold text-color-base" data-network-out>{{ $networkOut }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-color-muted flex items-center gap-1"><x-ri-download-2-line class="size-3" /> In</span>
                    <span class="text-sm font-semibold text-color-base" data-network-in>{{ $networkIn }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h3 class="text-lg font-bold text-color-base flex items-center gap-2">
                    <x-ri-settings-4-line class="size-5 text-primary" />
                    Resource & Feature Limits
                </h3>
                <p class="text-sm text-color-muted">Allocated resources and feature limitations for this server.</p>
            </div>
            
            @if(!empty($panelServerUrl))
                <a
                    href="{{ $panelServerUrl }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition-all duration-200"
                >
                    <x-ri-external-link-line class="size-4 mr-2" />
                    Access Control Panel
                </a>
            @else
                <button disabled class="inline-flex items-center justify-center rounded-xl bg-primary/50 px-5 py-2.5 text-sm font-semibold text-white cursor-not-allowed opacity-70">
                    <x-ri-prohibited-line class="size-4 mr-2" />
                    Access Panel
                </button>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($limitsRow as $label => $value)
                <div class="bg-background/50 border border-neutral/50 rounded-xl p-4 text-center transition-transform hover:scale-[1.02]">
                    <div class="inline-flex items-center justify-center p-2 rounded-full bg-neutral/10 text-color-muted mb-2">
                        <x-dynamic-component :component="'ri-' . ($limitIcons[$label] ?? 'question-line')" class="size-5" />
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-color-muted mb-1">{{ $label }}</p>
                    <p class="text-base font-bold text-color-base" data-limit-label="{{ strtolower($label) }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 flex items-start gap-2 text-sm text-color-muted bg-primary/5 border border-primary/10 p-4 rounded-xl">
            <x-ri-information-line class="size-5 text-primary flex-shrink-0 mt-0.5" />
            <p>
                For advanced management, file access, console output, and configuration, please access the dedicated Pterodactyl control panel using the button above.
            </p>
        </div>
    </div>

    @once
        @include('pterodactyl-sso::overview.partials.scripts')
    @endonce
</div>