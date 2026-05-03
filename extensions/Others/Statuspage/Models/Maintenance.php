<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'ext_statuspage_maintenances';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'started_at',
        'scheduled_at',
        'completed_at',
        'monitor_ids',
        'color',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'monitor_ids' => 'array',
    ];

    public function updates()
    {
        return $this->hasMany(MaintenanceUpdate::class, 'maintenance_id')
                    ->orderBy('created_at', 'desc');
    }

    public function isActive(): bool
    {
        return $this->status !== 'completed' && 
               $this->started_at !== null && 
               ($this->completed_at === null || $this->completed_at->isFuture());
    }
}
