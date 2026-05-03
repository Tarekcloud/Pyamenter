<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'ext_statuspage_incidents';

    protected $fillable = [
        'monitor_id',
        'title',
        'slug',
        'description',
        'status',
        'started_at',
        'resolved_at',
    ];


    protected $casts = [
        'started_at'  => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
