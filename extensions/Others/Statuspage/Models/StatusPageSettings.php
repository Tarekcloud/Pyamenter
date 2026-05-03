<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPageSettings extends Model
{
    protected $table = 'ext_statuspage_settings';

    protected $fillable = [
        'history_type',
        'history_days',
        'incidents_limit',
        'maintenance_limit',
        'up_color',
        'down_color',
        'degraded_color',
        'maintenance_color',
        'show_uptime_cards',
        'show_incidents',
        'show_maintenance',
        'category_sort_order',
    ];

    protected $casts = [
        'history_days' => 'integer',
        'incidents_limit' => 'integer',
        'maintenance_limit' => 'integer',
        'show_uptime_cards' => 'boolean',
        'show_incidents' => 'boolean',
        'show_maintenance' => 'boolean',
        'category_sort_order' => 'array',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'history_type' => 'days',
            'history_days' => 30,
            'incidents_limit' => 0,
            'maintenance_limit' => 0,
            'up_color' => '#16a34a',
            'down_color' => '#dc2626',
            'degraded_color' => '#f59e0b',
            'maintenance_color' => '#3b82f6',
            'show_uptime_cards' => true,
            'show_incidents' => true,
            'show_maintenance' => true,
        ]);
    }
}
