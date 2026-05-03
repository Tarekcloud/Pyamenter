<?php

namespace Paymenter\Extensions\Others\Gifts\Models;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gift extends Model
{
    protected $table = 'ext_gifts';

    protected $fillable = [
        'code',
        'type',
        'description',
        'coupon_id',
        'coupon_ids',
        'allow_coupon_selection',
        'credit_amount',
        'credit_min_amount',
        'credit_max_amount',
        'allow_credit_range',
        'currency_code',
        'currency_codes',
        'allow_currency_selection',
        'product_id',
        'plan_id',
        'trial_period',
        'trial_unit',
        'extension_period',
        'extension_min_period',
        'extension_max_period',
        'allow_extension_range',
        'extension_unit',
        'upgrade_product_id',
        'upgrade_plan_id',
        'discount_amount',
        'discount_min_amount',
        'discount_max_amount',
        'allow_discount_range',
        'discount_type',
        'discount_currency_code',
        'discount_minimum_order',
        'discount_maximum_discount',
        'discount_product_ids',
        'discount_category_ids',
        'discount_applies_to_all',
        'service_product_ids',
        'service_plan_ids',
        'allow_multiple_services',
        'max_uses',
        'max_uses_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'allow_user_selection',
    ];

    protected $casts = [
        'coupon_ids' => 'array',
        'credit_amount' => 'decimal:2',
        'credit_min_amount' => 'decimal:2',
        'credit_max_amount' => 'decimal:2',
        'currency_codes' => 'array',
        'discount_amount' => 'decimal:2',
        'discount_min_amount' => 'decimal:2',
        'discount_max_amount' => 'decimal:2',
        'discount_minimum_order' => 'decimal:2',
        'discount_maximum_discount' => 'decimal:2',
        'discount_product_ids' => 'array',
        'discount_category_ids' => 'array',
        'service_product_ids' => 'array',
        'service_plan_ids' => 'array',
        'trial_period' => 'integer',
        'extension_period' => 'integer',
        'extension_min_period' => 'integer',
        'extension_max_period' => 'integer',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'allow_user_selection' => 'boolean',
        'allow_coupon_selection' => 'boolean',
        'allow_credit_range' => 'boolean',
        'allow_currency_selection' => 'boolean',
        'allow_extension_range' => 'boolean',
        'allow_discount_range' => 'boolean',
        'allow_multiple_services' => 'boolean',
        'discount_applies_to_all' => 'boolean',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function upgradeProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'upgrade_product_id');
    }

    public function upgradePlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'upgrade_plan_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(GiftRedemption::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function hasExceededMaxUsesPerUser(int $userId): bool
    {
        if (!$this->max_uses_per_user) {
            return false;
        }

        $userRedemptions = $this->redemptions()
            ->where('user_id', $userId)
            ->count();

        return $userRedemptions >= $this->max_uses_per_user;
    }

    public function canBeRedeemedBy(int $userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->hasExceededMaxUsesPerUser($userId)) {
            return false;
        }

        return true;
    }
}
