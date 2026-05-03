<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceUpdate extends Model
{
    protected $table = 'ext_statuspage_maintenance_updates';

    protected $fillable = [
        'maintenance_id',
        'status',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
