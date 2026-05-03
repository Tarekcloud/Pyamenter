<?php

namespace Paymenter\Extensions\Others\Helpcenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FAQ extends Model
{
    protected $table = 'ext_hc_faqs';

    protected $fillable = [
        'article_id',
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
