<?php

namespace Paymenter\Extensions\Others\Helpcenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $table = 'ext_hc_articles';

    protected $fillable = [
        'title',
        'content',
        'htmlcontent',
        'rawcontent',
        'description',
        'published_at',
        'is_active',
        'slug',
        'category_id',
        'helpful_yes',
        'helpful_no',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'helpful_yes' => 'integer',
        'helpful_no' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(FAQ::class, 'article_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function voteHelpful(bool $isHelpful): void
    {
        if ($isHelpful) {
            $this->increment('helpful_yes');
        } else {
            $this->increment('helpful_no');
        }
    }

    public function totalFeedback(): int
    {
        return $this->helpful_yes + $this->helpful_no;
    }

    public function helpfulPercentage(): ?int
    {
        if ($this->totalFeedback() === 0) return null;
        return round(($this->helpful_yes / $this->totalFeedback()) * 100);
    }
}
