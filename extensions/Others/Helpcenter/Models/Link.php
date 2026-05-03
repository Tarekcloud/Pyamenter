<?php

namespace Paymenter\Extensions\Others\Helpcenter\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'ext_hc_links';

    protected $fillable = [
        'title',
        'url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;
}
