<?php

namespace Paymenter\Extensions\Others\Gifts\Services;

use App\Jobs\Server\CreateJob;
use App\Models\Coupon;
use App\Models\Credit;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Paymenter\Extensions\Others\Gifts\Models\Gift;
use Paymenter\Extensions\Others\Gifts\Models\GiftRedemption;

class GiftRedemptionService
{
    public function redeem(Gift $gift, User $user, array $selectedData = []): array
    {
        if (!$gift->canBeRedeemedBy($user->id)) {
            return [
                'success' => false,
                'message' => 'This gift code is not valid or has expired.',
            ];
        }

        return DB::transaction(function () use ($gift, $user, $selectedData) {
            try {
                $result = match ($gift->type) {
                    'coupon' => $this->redeemCoupon($gift, $user, $selectedData),
                    'credit' => $this->redeemCredit($gift, $user, $selectedData),
                    'service' => $this->redeemService($gift, $user, $selectedData),
                    'discount' => $this->redeemDiscount($gift, $user, $selectedData),
                    'extension' => $this->redeemExtension($gift, $user, $selectedData),
                    'upgrade' => $this->redeemUpgrade($gift, $user, $selectedData),
                    default => [
                        'success' => false,
                        'message' => 'Invalid gift code type.',
                    ],
                };

                if ($result['success']) {
                    $redemption = GiftRedemption::create([
                        'gift_id' => $gift->id,
                        'user_id' => $user->id,
                        'selected_service_id' => $selectedData['service_id'] ?? null,
                        'selected_product_id' => $selectedData['product_id'] ?? null,
                        'selected_plan_id' => $selectedData['plan_id'] ?? null,
                        'redeemed_at' => now(),
                        'notes' => $result['message'] ?? null,
                    ]);

                    $gift->increment('used_count');

                    if (class_exists(\Paymenter\Extensions\Others\Rewards\Services\RewardService::class)) {
                        \Illuminate\Support\Facades\Event::dispatch('gift.redeemed', $redemption);
                    }
                }

                return $result;
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'An error occurred while redeeming the gift code: ' . $e->getMessage(),
                ];
            }
        });
    }

    protected function redeemCoupon(Gift $gift, User $user, array $selectedData = []): array
    {
        $couponId = null;
        
        if ($gift->allow_coupon_selection && isset($selectedData['coupon_id'])) {
            $couponId = $selectedData['coupon_id'];
        } elseif ($gift->coupon_ids && is_array($gift->coupon_ids) && count($gift->coupon_ids) > 0) {
            $couponId = $gift->coupon_ids[0];
        } elseif ($gift->coupon_id) {
            $couponId = $gift->coupon_id;
        }

        if (!$couponId) {
            return [
                'success' => false,
                'message' => 'No coupon associated with this gift code.',
            ];
        }

        $coupon = Coupon::find($couponId);
        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Coupon not found.',
            ];
        }

        return [
            'success' => true,
            'message' => "Coupon code '{$coupon->code}' has been added to your account. You can use it during checkout.",
            'coupon_code' => $coupon->code,
        ];
    }

    protected function redeemCredit(Gift $gift, User $user, array $selectedData = []): array
    {
        $creditAmount = $gift->credit_amount;
        $currencyCode = $gift->currency_code;

        if ($gift->allow_credit_range) {
            $selectedAmount = $selectedData['credit_amount'] ?? null;
            if (!$selectedAmount || $selectedAmount < $gift->credit_min_amount || $selectedAmount > $gift->credit_max_amount) {
                return [
                    'success' => false,
                    'message' => "Please select a credit amount between {$gift->credit_min_amount} and {$gift->credit_max_amount}.",
                ];
            }
            $creditAmount = $selectedAmount;
        }

        if ($gift->allow_currency_selection) {
            $selectedCurrency = $selectedData['currency_code'] ?? null;
            if (!$selectedCurrency || !in_array($selectedCurrency, $gift->currency_codes ?? [])) {
                return [
                    'success' => false,
                    'message' => 'Please select a valid currency.',
                ];
            }
            $currencyCode = $selectedCurrency;
        }

        if (!$creditAmount || !$currencyCode) {
            return [
                'success' => false,
                'message' => 'Invalid credit amount or currency.',
            ];
        }

        $currency = Currency::where('code', $currencyCode)->first();
        if (!$currency) {
            return [
                'success' => false,
                'message' => 'Currency not found.',
            ];
        }

        $credit = Credit::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->first();

        if ($credit) {
            $credit->increment('amount', $creditAmount);
        } else {
            Credit::create([
                'user_id' => $user->id,
                'currency_code' => $currencyCode,
                'amount' => $creditAmount,
            ]);
        }

        return [
            'success' => true,
            'message' => "Credit of {$creditAmount} {$currencyCode} has been added to your account.",
        ];
    }

    protected function redeemService(Gift $gift, User $user, array $selectedData = []): array
    {
        $productId = $gift->allow_user_selection && isset($selectedData['product_id']) 
            ? $selectedData['product_id'] 
            : $gift->product_id;
        $planId = $gift->allow_user_selection && isset($selectedData['plan_id']) 
            ? $selectedData['plan_id'] 
            : $gift->plan_id;

        if (!$productId || !$planId) {
            return [
                'success' => false,
                'message' => 'No product or plan associated with this gift code.',
            ];
        }

        $product = Product::find($productId);
        $plan = Plan::find($planId);

        if (!$product || !$plan) {
            return [
                'success' => false,
                'message' => 'Product or plan not found.',
            ];
        }

        $currencyCode = config('settings.default_currency', 'USD');

        $order = Order::create([
            'user_id' => $user->id,
            'currency_code' => $currencyCode,
        ]);

        $service = $order->services()->create([
            'user_id' => $user->id,
            'currency_code' => $currencyCode,
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'price' => 0,
            'quantity' => 1,
        ]);

        if ($product->server) {
            CreateJob::dispatch($service);
        }
        
        $service->status = Service::STATUS_ACTIVE;
        
        if ($gift->trial_period && $gift->trial_unit) {
            $expiresAt = now()->{'add' . ucfirst($gift->trial_unit) . 's'}($gift->trial_period);
            $service->expires_at = $expiresAt;
        } else {
            $service->expires_at = $service->calculateNextDueDate();
        }
        
        $service->save();

        $trialMessage = $gift->trial_period && $gift->trial_unit 
            ? " (active for {$gift->trial_period} {$gift->trial_unit}(s))"
            : '';

        return [
            'success' => true,
            'message' => "Free service '{$product->name}' has been activated in your account{$trialMessage}.",
            'service_id' => $service->id,
        ];
    }

    protected function redeemDiscount(Gift $gift, User $user, array $selectedData = []): array
    {
        $discountAmount = $gift->discount_amount;
        $discountType = $gift->discount_type;

        if ($gift->allow_discount_range) {
            $selectedAmount = $selectedData['discount_amount'] ?? null;
            if (!$selectedAmount || $selectedAmount < $gift->discount_min_amount || $selectedAmount > $gift->discount_max_amount) {
                return [
                    'success' => false,
                    'message' => "Please select a discount amount between {$gift->discount_min_amount} and {$gift->discount_max_amount}.",
                ];
            }
            $discountAmount = $selectedAmount;
        }

        if (!$discountAmount || !$discountType) {
            return [
                'success' => false,
                'message' => 'Invalid discount amount or type.',
            ];
        }

        $couponCode = 'GIFT-' . strtoupper(substr(md5($gift->code . $user->id . time()), 0, 8));

        $couponData = [
            'code' => $couponCode,
            'type' => $discountType === 'percentage' ? 'percentage' : 'fixed',
            'value' => $discountAmount,
            'currency_code' => $gift->discount_currency_code,
            'max_uses' => 1,
            'max_uses_per_user' => 1,
            'starts_at' => now(),
            'expires_at' => $gift->expires_at,
            'is_active' => true,
        ];

        if ($gift->discount_minimum_order) {
            $couponData['minimum'] = $gift->discount_minimum_order;
        }

        if ($gift->discount_maximum_discount) {
            $couponData['maximum'] = $gift->discount_maximum_discount;
        }

        if (!$gift->discount_applies_to_all) {
            if ($gift->discount_product_ids) {
                $couponData['products'] = $gift->discount_product_ids;
            }
            if ($gift->discount_category_ids) {
                $couponData['categories'] = $gift->discount_category_ids;
            }
        }

        $coupon = Coupon::create($couponData);

        return [
            'success' => true,
            'message' => "Discount coupon '{$couponCode}' has been created. You can use it during checkout.",
            'coupon_code' => $couponCode,
        ];
    }

    protected function redeemExtension(Gift $gift, User $user, array $selectedData = []): array
    {
        $extensionPeriod = $gift->extension_period;
        $extensionUnit = $gift->extension_unit;

        if ($gift->allow_extension_range) {
            $selectedPeriod = $selectedData['extension_period'] ?? null;
            if (!$selectedPeriod || $selectedPeriod < $gift->extension_min_period || $selectedPeriod > $gift->extension_max_period) {
                return [
                    'success' => false,
                    'message' => "Please select an extension period between {$gift->extension_min_period} and {$gift->extension_max_period}.",
                ];
            }
            $extensionPeriod = $selectedPeriod;
        }

        if (!$extensionPeriod || !$extensionUnit) {
            return [
                'success' => false,
                'message' => 'Invalid extension period or unit.',
            ];
        }

        $services = collect();
        
        if ($gift->allow_user_selection && isset($selectedData['service_id'])) {
            $service = Service::where('id', $selectedData['service_id'])
                ->where('user_id', $user->id)
                ->where('status', Service::STATUS_ACTIVE)
                ->first();
            
            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Selected service not found or not active.',
                ];
            }
            $services->push($service);
        } else {
            $services = Service::where('user_id', $user->id)
                ->where('status', Service::STATUS_ACTIVE)
                ->get();
        }

        if ($services->isEmpty()) {
            return [
                'success' => false,
                'message' => 'You do not have any active services to extend.',
            ];
        }

        $extensionMethod = 'add' . ucfirst($extensionUnit) . 's';
        $extendedCount = 0;

        foreach ($services as $service) {
            if ($service->expires_at) {
                $service->expires_at = $service->expires_at->{$extensionMethod}($extensionPeriod);
            } else {
                $service->expires_at = now()->{$extensionMethod}($extensionPeriod);
            }
            $service->save();
            $extendedCount++;
        }

        $message = $gift->allow_user_selection 
            ? "Successfully extended service by {$extensionPeriod} {$extensionUnit}(s)."
            : "Successfully extended {$extendedCount} service(s) by {$extensionPeriod} {$extensionUnit}(s).";

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    protected function redeemUpgrade(Gift $gift, User $user, array $selectedData = []): array
    {
        $service = null;
        
        if ($gift->allow_user_selection && isset($selectedData['service_id'])) {
            $service = Service::where('id', $selectedData['service_id'])
                ->where('user_id', $user->id)
                ->where('status', Service::STATUS_ACTIVE)
                ->with('plan')
                ->first();
            
            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Selected service not found or not active.',
                ];
            }
        } else {
            if (!$gift->upgrade_product_id) {
                return [
                    'success' => false,
                    'message' => 'No upgrade product specified.',
                ];
            }

            $services = Service::where('user_id', $user->id)
                ->where('status', Service::STATUS_ACTIVE)
                ->where('product_id', $gift->upgrade_product_id)
                ->with('plan')
                ->get();

            if ($services->isEmpty()) {
                $upgradeProduct = Product::find($gift->upgrade_product_id);
                return [
                    'success' => false,
                    'message' => "You do not have any active services for product '{$upgradeProduct->name}' to upgrade.",
                ];
            }

            $service = $services->first();
        }

        $currentPlan = $service->plan;
        if (!$currentPlan) {
            return [
                'success' => false,
                'message' => 'Current service plan not found.',
            ];
        }

        $currentPrice = $currentPlan->price();
        if (!$currentPrice || !$currentPrice->available) {
            return [
                'success' => false,
                'message' => 'Current plan price not available.',
            ];
        }
        $currentPriceValue = $currentPrice->price;

        $upgradePlan = null;

        if ($gift->upgrade_plan_id) {
            $upgradePlan = Plan::find($gift->upgrade_plan_id);
            if (!$upgradePlan) {
                return [
                    'success' => false,
                    'message' => 'Upgrade plan not found.',
                ];
            }

            if ($upgradePlan->priceable_id != $service->product_id || $upgradePlan->priceable_type != Product::class) {
                return [
                    'success' => false,
                    'message' => 'Upgrade plan does not belong to the service product.',
                ];
            }

            $upgradePrice = $upgradePlan->price();
            if (!$upgradePrice || !$upgradePrice->available) {
                return [
                    'success' => false,
                    'message' => 'Upgrade plan price not available.',
                ];
            }

            if ($upgradePrice->price <= $currentPriceValue) {
                return [
                    'success' => false,
                    'message' => 'Selected plan is not higher than the current plan.',
                ];
            }
        } else {
            $availablePlans = Plan::where('priceable_type', Product::class)
                ->where('priceable_id', $service->product_id)
                ->get()
                ->filter(function ($plan) use ($currentPriceValue) {
                    $planPrice = $plan->price();
                    return $planPrice && $planPrice->available && $planPrice->price > $currentPriceValue;
                })
                ->sortBy(function ($plan) {
                    return $plan->price()->price;
                })
                ->values();

            if ($availablePlans->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No higher plan available for this service.',
                ];
            }

            $upgradePlan = $availablePlans->first();
        }
        
        $serviceUpgrade = ServiceUpgrade::create([
            'service_id' => $service->id,
            'product_id' => $service->product_id,
            'plan_id' => $upgradePlan->id,
            'status' => ServiceUpgrade::STATUS_PENDING,
        ]);

        $service->plan_id = $upgradePlan->id;
        $service->save();

        return [
            'success' => true,
            'message' => "Service '{$service->product->name}' has been upgraded from '{$currentPlan->name}' to '{$upgradePlan->name}'.",
            'service_id' => $service->id,
        ];
    }
}
