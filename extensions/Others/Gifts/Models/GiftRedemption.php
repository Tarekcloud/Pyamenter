<?php

namespace Paymenter\Extensions\Others\Gifts\Models;

use App\Models\Plan;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftRedemption extends Model
{
    protected $table = 'ext_gift_redemptions';

    protected $fillable = [
        'gift_id',
        'user_id',
        'selected_service_id',
        'selected_product_id',
        'selected_plan_id',
        'redeemed_at',
        'notes',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function selectedService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'selected_service_id');
    }

    public function selectedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'selected_product_id');
    }

    public function selectedPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'selected_plan_id');
    }
}
