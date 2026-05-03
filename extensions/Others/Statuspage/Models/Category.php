<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'ext_statuspage_categories';

    protected $fillable = ['name', 'description'];

    public function monitors()
    {
        return $this->hasMany(Monitor::class);
    }
}
