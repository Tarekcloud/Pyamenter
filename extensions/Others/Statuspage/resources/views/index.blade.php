<div style="max-width:1200px; margin:2rem auto; padding:0 1rem;" class="statuspage-container">

<style>
    @media (max-width: 640px) {
        .statuspage-container {
            padding: 0 0.75rem !important;
            margin: 1rem auto !important;
        }
        
        .history-bars-desktop {
            display: none !important;
        }
        .history-bars-mobile {
            display: flex !important;
            justify-content: flex-end !important;
        }
        
        .monitor-card {
            flex-direction: row !important;
            align-items: center !important;
            padding: 1rem !important;
            gap: 0.75rem !important;
        }
        
        .monitor-card > div:first-child {
            flex: 0 0 auto !important;
            min-width: 0 !important;
            flex-wrap: nowrap;
        }
        
        .monitor-card > div:first-child > span:last-child {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .history-bars {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            justify-content: flex-end !important;
            margin-top: 0 !important;
        }
        
        .history-bars > div {
            display: flex;
            gap: 0.25rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        
        .last-checked-time {
            display: none !important;
        }
        
        h2 {
            font-size: 1.25rem !important;
            margin-bottom: 0.75rem !important;
        }
    }
    
    @media (min-width: 641px) {
        .history-bars-mobile {
            display: none !important;
        }
    }

    .history-bar {
        transition: transform 0.2s ease;
        position: relative;
        cursor: pointer;
    }
    .history-bar:hover {
        transform: scaleY(1.5);
    }
    .monitor-card::after {
        content: '';
        position: absolute;
        top:0; left:0; right:0; bottom:0;
        background-color: rgba(22, 163, 74, 0.15);
        border-radius: 0.5rem;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .monitor-card:hover::after {
        opacity: 1;
    }
</style>

@php
    $upColor = $settings->up_color ?? '#16a34a';
    $downColor = $settings->down_color ?? '#dc2626';
    $degradedColor = $settings->degraded_color ?? '#f59e0b';
    
    function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }
    
    function lightenColor($hex, $percent = 85) {
        $rgb = hexToRgb($hex);
        $r = min(255, $rgb['r'] + (255 - $rgb['r']) * ($percent / 100));
        $g = min(255, $rgb['g'] + (255 - $rgb['g']) * ($percent / 100));
        $b = min(255, $rgb['b'] + (255 - $rgb['b']) * ($percent / 100));
        return sprintf('rgb(%d, %d, %d)', round($r), round($g), round($b));
    }
    
    $statusText = 'All systems operational';
    $statusBg = lightenColor($upColor, 85);
    $statusColor = $upColor;
    $icon = '<x-ri-check-line class="w-6 h-6" />';

    if($overall_status === 'maintenance') {
        $statusText = isset($active_maintenance) ? 'Maintenance in progress: ' . $active_maintenance->title : 'Maintenance in progress';
        $statusBg = '#dbeafe';
        $statusColor = '#1e40af';
        $icon = '<x-ri-tools-line class="w-6 h-6" />';
    } elseif($overall_status === 'partial') {
        $statusText = 'Partial outage';
        $statusBg = lightenColor($degradedColor, 85);
        $statusColor = $degradedColor;
        $icon = '<x-ri-error-warning-line class="w-6 h-6" />';
    } elseif($overall_status === 'major') {
        $statusText = 'Major outage';
        $statusBg = lightenColor($downColor, 85);
        $statusColor = $downColor;
        $icon = '<x-ri-alarm-warning-line class="w-6 h-6" />';
    }
@endphp

<div style="background-color:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusColor }}; padding:1rem 1.5rem; border-radius:0.5rem; display:flex; justify-content:center; align-items:center; font-weight:600; margin-bottom:2rem; text-align:center;">
    <span style="font-size:1.5rem; margin-right:0.5rem;">{!! $icon !!}</span>
    <span style="font-size:1.125rem;">{{ $statusText }}</span>
</div>

@foreach($categories as $category)
    <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:1rem; color:var(--text-primary);">{{ $category->name }}</h2>
    <div style="margin-bottom:2rem;">
        @foreach($category->monitors as $monitor)
            @php
                $isInMaintenance = false;
                $maintenanceColor = $settings->maintenance_color ?? '#3b82f6';
                if (isset($active_maintenance) && $active_maintenance && $active_maintenance->monitor_ids) {
                    $isInMaintenance = in_array($monitor->id, $active_maintenance->monitor_ids);
                }
                
                $badgeColor = $maintenanceColor;
                if (!$isInMaintenance) {
                    $badgeColor = $monitor->last_status === 'up' ? ($settings->up_color ?? '#16a34a') : ($settings->down_color ?? '#ef4444');
                }
            @endphp
            <div class="monitor-card bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; box-shadow:0 2px 6px rgba(0,0,0,0.08); position:relative; flex-wrap:wrap; gap:0.5rem;">
                <div style="display:flex; align-items:center; flex:1; min-width:200px; gap:0.5rem; flex-shrink:0;">
                    @if($settings->show_uptime_cards ?? true)
                    <span style="background-color:{{ $badgeColor }}; color:white; padding:0.25rem 0.5rem; border-radius:0.375rem; font-weight:600; font-size:0.875rem; white-space:nowrap;">
                        {{ number_format($monitor->uptime,2) }}%
                    </span>
                    @endif
                    <span style="font-weight:600; font-size:1rem; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $monitor->name }}</span>
                </div>
                <div class="history-bars" style="display:flex; align-items:center; gap:0.25rem; flex:2; min-width:200px; justify-content:flex-end; flex-wrap:wrap; flex-shrink:1;">
                    <div class="history-bars-desktop" style="display:flex; gap:0.25rem;">
                        @php
                            $thresholdDate = now();
                            $expectedBars = 0;
                            
                            if ($settings->history_type === 'hours') {
                                $hours = (int)$settings->history_days;
                                $thresholdDate = now()->subHours($hours);
                                $period = 'hour';
                                $expectedBars = $hours;
                            } else {
                                $thresholdDate = now()->subDays($settings->history_days);
                                $period = 'day';
                                $expectedBars = (int)$settings->history_days;
                            }
                            
                            $aggregatedHistory = $monitor->getAggregatedHistory($period, $thresholdDate);
                            
                            $allPeriods = collect();
                            $currentDate = $thresholdDate->copy();
                            $periodsGenerated = 0;
                            
                            while ($periodsGenerated < $expectedBars && $currentDate <= now()) {
                                $periodKey = '';
                                $periodStart = $currentDate->copy();
                                
                                switch ($period) {
                                    case 'hour':
                                        $periodKey = $currentDate->format('Y-m-d H:00:00');
                                        $currentDate->addHour();
                                        break;
                                    case 'day':
                                        $periodKey = $currentDate->format('Y-m-d');
                                        $currentDate->addDay();
                                        break;
                                    case 'week':
                                        $periodKey = $currentDate->startOfWeek()->format('Y-m-d');
                                        $currentDate->addWeek();
                                        break;
                                }
                                
                                $historyItem = $aggregatedHistory->firstWhere('period', $periodKey);
                                $allPeriods->push((object) [
                                    'period' => $periodKey,
                                    'status' => $historyItem ? $historyItem->status : null,
                                    'checked_at' => $historyItem ? $historyItem->checked_at : $periodStart,
                                    'has_data' => $historyItem !== null,
                                ]);
                                
                                $periodsGenerated++;
                            }
                            
                            $allPeriodsMobile = collect();
                            if ($settings->history_type === 'days' && $settings->history_days > 7) {
                                $weekHistory = $monitor->getAggregatedHistory('week', $thresholdDate);
                                $currentWeek = $thresholdDate->copy()->startOfWeek();
                                $weeksNeeded = (int)ceil($settings->history_days / 7);
                                
                                for ($i = 0; $i < $weeksNeeded && $currentWeek <= now(); $i++) {
                                    $weekKey = $currentWeek->format('Y-m-d');
                                    $weekItem = $weekHistory->firstWhere('period', $weekKey);
                                    $allPeriodsMobile->push((object) [
                                        'period' => $weekKey,
                                        'status' => $weekItem ? $weekItem->status : null,
                                        'checked_at' => $weekItem ? $weekItem->checked_at : $currentWeek,
                                        'has_data' => $weekItem !== null,
                                    ]);
                                    $currentWeek->addWeek();
                                }
                            } else {
                                $allPeriodsMobile = $allPeriods;
                            }
                        @endphp
                        
                        @foreach($allPeriods as $periodItem)
                            @if($periodItem->has_data)
                                @php
                                    $barColor = $maintenanceColor;
                                    if (!$isInMaintenance) {
                                        $barColor = $periodItem->status === 'up' ? ($settings->up_color ?? '#16a34a') : ($settings->down_color ?? '#dc2626');
                                    }
                                @endphp
                                <div class="history-bar" style="width:10px; height:24px; border-radius:2px; background-color: {{ $barColor }};" ></div>
                            @else
                                <div class="history-bar" style="width:10px; height:24px; border-radius:2px; background-color: #9ca3af; opacity: 0.5;" ></div>
                            @endif
                        @endforeach
                        
                        @if($allPeriods->isEmpty())
                            <span style="font-size:0.75rem; color:var(--text-secondary); font-style:italic;">No history</span>
                        @endif
                    </div>
                    
                    @if($allPeriodsMobile->count() !== $allPeriods->count())
                    <div class="history-bars-mobile" style="display:none; gap:0.25rem; flex-direction:row;">
                        @foreach($allPeriodsMobile as $periodItem)
                            @if($periodItem->has_data)
                                @php
                                    $mobileBarColor = $maintenanceColor;
                                    if (!$isInMaintenance) {
                                        $mobileBarColor = $periodItem->status === 'up' ? ($settings->up_color ?? '#16a34a') : ($settings->down_color ?? '#dc2626');
                                    }
                                @endphp
                                <div class="history-bar" style="width:10px; height:24px; border-radius:2px; background-color: {{ $mobileBarColor }};" ></div>
                            @else
                                <div class="history-bar" style="width:10px; height:24px; border-radius:2px; background-color: #9ca3af; opacity: 0.5;" ></div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                        @if($monitor->history->isNotEmpty())
                            <span class="last-checked-time" style="font-size:0.75rem; color:var(--text-secondary);">{{ optional($monitor->history->first()->checked_at)->diffForHumans() ?? '-' }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endforeach

@if($settings->show_incidents ?? true)
<h2 style="font-size:1.5rem; font-weight:700; margin-bottom:1rem; color:var(--text-primary);">Incidents</h2>
<div style="display:flex; flex-direction:column; gap:1rem;">
    @forelse($incidents as $incident)
        <div class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg" style="box-shadow:0 2px 6px rgba(0,0,0,0.08);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                <h3 style="font-weight:600; font-size:1rem; color:var(--text-primary);">{{ $incident->title }}</h3>
                <span style="font-size:0.75rem; font-weight:700; padding:0.25rem 0.5rem; border-radius:0.375rem; background-color:{{ !$incident->resolved_at ? ($settings->down_color ?? '#dc2626') : ($settings->up_color ?? '#16a34a') }}; color:white;">
                    {{ $incident->resolved_at ? 'Resolved' : 'Active' }}
                </span>
            </div>
            <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:0.5rem;">
                {{ optional($incident->started_at)->format('d-m-Y H:i') ?? '-' }}
                @if($incident->resolved_at)
                    – {{ optional($incident->resolved_at)->format('d-m-Y H:i') ?? '-' }}
                @endif
            </p>
            <p style="color:var(--text-primary); font-size:0.875rem;">{{ $incident->description }}</p>
        </div>
        @empty
            <p style="color:var(--text-secondary);">No incidents reported</p>
        @endforelse
</div>
@endif

@if($settings->show_maintenance ?? true)
<h2 style="font-size:1.5rem; font-weight:700; margin-bottom:1rem; color:var(--text-primary); margin-top:2rem;">Maintenance</h2>
<div style="display:flex; flex-direction:column; gap:1rem;">
    @forelse($maintenances as $maintenance)
        <div class="bg-background-secondary dark:bg-background-secondary/80 border border-neutral p-4 rounded-lg" style="box-shadow:0 2px 6px rgba(0,0,0,0.08);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                <h3 style="font-weight:600; font-size:1rem; color:var(--text-primary);">{{ $maintenance->title }}</h3>
                <span style="font-size:0.75rem; font-weight:700; padding:0.25rem 0.5rem; border-radius:0.375rem; background-color:{{ $maintenance->status === 'completed' ? ($settings->up_color ?? '#16a34a') : ($settings->maintenance_color ?? '#3b82f6') }}; color:white;">
                    {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                </span>
            </div>
            <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:0.5rem;">
                @if($maintenance->scheduled_at)
                    Scheduled: {{ optional($maintenance->scheduled_at)->format('d-m-Y H:i') ?? '-' }}
                @endif
                @if($maintenance->started_at)
                    @if($maintenance->scheduled_at) | @endif
                    Started: {{ optional($maintenance->started_at)->format('d-m-Y H:i') ?? '-' }}
                @endif
                @if($maintenance->completed_at)
                    | Completed: {{ optional($maintenance->completed_at)->format('d-m-Y H:i') ?? '-' }}
                @endif
            </p>
            @if($maintenance->description)
                <p style="color:var(--text-primary); font-size:0.875rem;">{{ $maintenance->description }}</p>
            @endif
        </div>
        @empty
            <p style="color:var(--text-secondary);">No maintenance scheduled</p>
        @endforelse
</div>
@endif
</div>
