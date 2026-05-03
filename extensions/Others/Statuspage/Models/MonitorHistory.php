<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorHistory extends Model
{
    protected $table = 'ext_statuspage_monitor_history';

    protected $fillable = [
        'monitor_id',
        'status',
        'checked_at', 
    ];


    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
