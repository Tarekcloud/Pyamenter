<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Coupon extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $fillable = [
        'type',
        'time',
        'code',
        'value',
        'max_uses',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'recurring',
        'billing_periods',
        'applies_to', // 确保这个字段也在里面
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'value' => 'float',
        'billing_periods' => 'array', // 确保自动转为数组
    ];

    /**
     * Get the products that belong to the option.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * 新增：校验优惠码是否适用于指定的付款周期 (Plan)
     *
     * @param \App\Models\Plan|int|null $plan
     * @return bool
     */
    public function isValidForPlan($plan): bool
    {
        // 如果没有设置 billing_periods，或者为空数组，说明所有周期均可使用
        if (empty($this->billing_periods)) {
            return true;
        }

        // 如果传入的是 plan_id，则先获取 Plan 实例
        if (is_numeric($plan)) {
            $plan = \App\Models\Plan::find($plan);
        }

        if (!$plan) {
            return false;
        }

        // 对比套餐的 billing_unit 是否在优惠码允许的数组中
        return in_array($plan->billing_unit, $this->billing_periods);
    }

    /**
     * Check if the user has exceeded the maximum allowed uses of this coupon
     *
     * @param  int  $userId
     */
    public function hasExceededMaxUsesPerUser($userId): bool
    {
        if (empty($this->max_uses_per_user)) {
            return false;
        }

        return $this->services()
            ->where('user_id', $userId)
            ->count() >= $this->max_uses_per_user;
    }

    public function calculateDiscount($price, $type = 'price', $planId = null)
    {
        if (!in_array($type, ['price', 'setup_fee'])) {
            throw new \InvalidArgumentException('Invalid type for coupon discount calculation');
        }
        
        // 兼容 applies_to 可能为空的情况 (旧数据)
        $appliesTo = $this->applies_to ?? 'all';
        if (!in_array($appliesTo, ['all', $type])) {
            return 0;
        }

        // 新增：如果有提供 planId，则校验付款周期是否符合要求。不符合直接返回 0 折扣。
        if ($planId && !$this->isValidForPlan($planId)) {
            return 0;
        }

        $discount = 0;
        if ($this->type === 'percentage') {
            $discount = $price * $this->value / 100;
        } elseif ($this->type === 'fixed') {
            $discount = $this->value;
        }
        if ($price < $discount) {
            $discount = $price;
        }

        return $discount;
    }
}