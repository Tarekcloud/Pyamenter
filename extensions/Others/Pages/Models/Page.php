<?php

namespace Paymenter\Extensions\Others\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'ext_pages';
    protected $fillable = [
        'slug',
        'title',
        'description',
        'image',
        'content',
        'visible',
        'as_html',
        'visibility',
        'navigation',
        'sort',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'html' => 'boolean',
        'visibility' => 'string',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
