<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    protected $table = 'ext_statuspage_monitors';

    protected $fillable = [
        'name',
        'description',
        'type',
        'url',
        'host',
        'port',
        'keyword',
        'response',
        'interval',
        'retries',
        'timeout',
        'last_status',
        'last_checked_at',
        'category',
        'sort_order',
    ];

   
    public function history()
    {
        return $this->hasMany(MonitorHistory::class, 'monitor_id')
                    ->orderBy('checked_at', 'desc');
    }

   
    public function getUptimeAttribute()
    {
        $settings = \Paymenter\Extensions\Others\Statuspage\Models\StatusPageSettings::getSettings();
        
        $thresholdDate = now();
        if ($settings->history_type === 'hours') {
            $hours = (int)$settings->history_days;
            $thresholdDate = now()->subHours($hours);
        } else {
            $thresholdDate = now()->subDays($settings->history_days);
        }
        
        $history = $this->relationLoaded('history') 
            ? $this->history->where('checked_at', '>=', $thresholdDate)
            : $this->history()->where('checked_at', '>=', $thresholdDate)->orderBy('checked_at', 'desc')->get();
            
        if ($history->isEmpty()) return 0;

        $upCount = $history->where('status', 'up')->count();
        return ($upCount / $history->count()) * 100;
    }

    
    public static function getCategoryGroups()
    {
        $settings = \Paymenter\Extensions\Others\Statuspage\Models\StatusPageSettings::getSettings();
        
        $thresholdDate = now();
        if ($settings->history_type === 'hours') {
            $hours = (int)$settings->history_days;
            $thresholdDate = now()->subHours($hours);
        } else {
            $thresholdDate = now()->subDays($settings->history_days);
        }
        
        $monitors = self::with(['history' => function ($query) use ($thresholdDate) {
            $query->where('checked_at', '>=', $thresholdDate)
                  ->orderBy('checked_at', 'desc');
        }])->orderBy('sort_order')->orderBy('name')->get();

        $categorySortOrder = $settings->category_sort_order ?? [];
        
        $grouped = $monitors->groupBy('category')->map(function ($monitors, $categoryName) {
            return (object) [
                'name' => $categoryName ?: 'Uncategorized',
                'monitors' => $monitors->sortBy('sort_order')->values(),
            ];
        });

        if (!empty($categorySortOrder) && is_array($categorySortOrder)) {
            $sorted = $grouped->sortBy(function ($category) use ($categorySortOrder) {
                $index = array_search($category->name, $categorySortOrder);
                return $index !== false ? $index : 999;
            });
            return collect($sorted->values());
        }

        return collect($grouped->sortBy('name')->values());
    }

   
    public function notifications()
    {
        return $this->belongsToMany(
            Notification::class,
            'ext_statuspage_monitor_notification'
        );
    }

    public function getAggregatedHistory(string $period, $startDate)
    {
        $history = $this->relationLoaded('history') 
            ? $this->history->where('checked_at', '>=', $startDate)
            : $this->history()->where('checked_at', '>=', $startDate)->orderBy('checked_at', 'asc')->get();

        if ($history->isEmpty()) {
            return collect();
        }

        $grouped = $history->groupBy(function ($item) use ($period) {
            $date = $item->checked_at->copy();
            switch ($period) {
                case 'hour':
                    return $date->format('Y-m-d H:00:00');
                case 'day':
                    return $date->format('Y-m-d');
                case 'week':
                    return $date->startOfWeek()->format('Y-m-d');
                default:
                    return $date->format('Y-m-d');
            }
        });

        return $grouped->map(function ($periodHistory, $periodKey) {
            $hasDown = $periodHistory->contains('status', 'down');
            $lastCheck = $periodHistory->last();
            
            return (object) [
                'period' => $periodKey,
                'status' => $hasDown ? 'down' : 'up',
                'checked_at' => $lastCheck->checked_at,
                'count' => $periodHistory->count(),
            ];
        })->values();
    }
}
