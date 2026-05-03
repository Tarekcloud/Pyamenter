<?php

namespace Paymenter\Extensions\Others\Gifts\Livewire\Gifts;

use App\Livewire\Component;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\Gifts\Models\Gift;
use Paymenter\Extensions\Others\Gifts\Services\GiftRedemptionService;

class Redeem extends Component
{
    public $code = '';
    public $message = '';
    public $messageType = '';
    public $gift = null;
    public $showSelection = false;
    public $autoRedeem = false;
    
    public $selectedServiceId = null;
    public $selectedProductId = null;
    public $selectedPlanId = null;
    public $selectedCouponId = null;
    public $selectedCreditAmount = null;
    public $selectedCurrencyCode = null;
    public $selectedDiscountAmount = null;
    public $selectedExtensionPeriod = null;

    public function checkCode()
    {
        $this->validate([
            'code' => 'required|string|max:255',
        ]);

        $cleanCode = strtoupper(trim($this->code));
        $this->gift = Gift::where('code', $cleanCode)->first();

        if (!$this->gift) {
            $this->message = 'Gift code not found.';
            $this->messageType = 'error';
            $this->showSelection = false;
            return;
        }

        if (!$this->gift->isValid()) {
            $this->message = 'This gift code is not valid or has expired.';
            $this->messageType = 'error';
            $this->showSelection = false;
            return;
        }

        $user = Auth::user();
        if (!$this->gift->canBeRedeemedBy($user->id)) {
            $this->message = 'You cannot redeem this gift code.';
            $this->messageType = 'error';
            $this->showSelection = false;
            return;
        }

        if ($this->gift && (
            ($this->gift->allow_user_selection && in_array($this->gift->type, ['service', 'extension', 'upgrade'])) ||
            ($this->gift->allow_coupon_selection && $this->gift->type === 'coupon') ||
            ($this->gift->allow_credit_range && $this->gift->type === 'credit') ||
            ($this->gift->allow_currency_selection && $this->gift->type === 'credit') ||
            ($this->gift->allow_discount_range && $this->gift->type === 'discount') ||
            ($this->gift->allow_extension_range && $this->gift->type === 'extension')
        )) {
            $this->showSelection = true;
            $this->message = '';
        } else {
            $this->redeem();
        }
    }

    public function redeem()
    {
        if ($this->gift && $this->showSelection) {
            $this->validate([
                'code' => 'required|string|max:255',
            ]);

            if ($this->gift->type === 'service' && $this->gift->allow_user_selection && (!$this->selectedProductId || !$this->selectedPlanId)) {
                $this->message = 'Please select a product and plan.';
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'extension' && $this->gift->allow_user_selection && !$this->selectedServiceId) {
                $this->message = 'Please select a service to extend.';
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'extension' && $this->gift->allow_extension_range && (!$this->selectedExtensionPeriod || $this->selectedExtensionPeriod < $this->gift->extension_min_period || $this->selectedExtensionPeriod > $this->gift->extension_max_period)) {
                $this->message = "Please select an extension period between {$this->gift->extension_min_period} and {$this->gift->extension_max_period}.";
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'upgrade' && $this->gift->allow_user_selection && !$this->selectedServiceId) {
                $this->message = 'Please select a service to upgrade.';
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'coupon' && $this->gift->allow_coupon_selection && !$this->selectedCouponId) {
                $this->message = 'Please select a coupon.';
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'credit' && $this->gift->allow_credit_range && (!$this->selectedCreditAmount || $this->selectedCreditAmount < $this->gift->credit_min_amount || $this->selectedCreditAmount > $this->gift->credit_max_amount)) {
                $this->message = "Please select a credit amount between {$this->gift->credit_min_amount} and {$this->gift->credit_max_amount}.";
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'credit' && $this->gift->allow_currency_selection && !$this->selectedCurrencyCode) {
                $this->message = 'Please select a currency.';
                $this->messageType = 'error';
                return;
            }

            if ($this->gift->type === 'discount' && $this->gift->allow_discount_range && (!$this->selectedDiscountAmount || $this->selectedDiscountAmount < $this->gift->discount_min_amount || $this->selectedDiscountAmount > $this->gift->discount_max_amount)) {
                $this->message = "Please select a discount amount between {$this->gift->discount_min_amount} and {$this->gift->discount_max_amount}.";
                $this->messageType = 'error';
                return;
            }
        } else {
            $this->validate([
                'code' => 'required|string|max:255',
            ]);

            $cleanCode = strtoupper(trim($this->code));
            $this->gift = Gift::where('code', $cleanCode)->first();

            if (!$this->gift) {
                $this->message = 'Gift code not found.';
                $this->messageType = 'error';
                return;
            }
        }

        $user = Auth::user();
        $redemptionService = new GiftRedemptionService();
        
        $selectedData = [
            'service_id' => $this->selectedServiceId,
            'product_id' => $this->selectedProductId,
            'plan_id' => $this->selectedPlanId,
            'coupon_id' => $this->selectedCouponId,
            'credit_amount' => $this->selectedCreditAmount,
            'currency_code' => $this->selectedCurrencyCode,
            'discount_amount' => $this->selectedDiscountAmount,
            'extension_period' => $this->selectedExtensionPeriod,
        ];
        
        $result = $redemptionService->redeem($this->gift, $user, $selectedData);

        if ($result['success']) {
            $this->message = $result['message'];
            $this->messageType = 'success';
            $this->code = '';
            $this->showSelection = false;
            $this->gift = null;
            $this->resetSelections();
        } else {
            $this->message = $result['message'];
            $this->messageType = 'error';
        }
    }

    protected function resetSelections()
    {
        $this->selectedServiceId = null;
        $this->selectedProductId = null;
        $this->selectedPlanId = null;
        $this->selectedCouponId = null;
        $this->selectedCreditAmount = null;
        $this->selectedCurrencyCode = null;
        $this->selectedDiscountAmount = null;
        $this->selectedExtensionPeriod = null;
    }

    public function getServicesProperty()
    {
        if (!Auth::check()) {
            return collect();
        }

        return Service::where('user_id', Auth::id())
            ->where('status', Service::STATUS_ACTIVE)
            ->with('product', 'plan')
            ->get();
    }

    public function getProductsProperty()
    {
        return Product::all();
    }

    public function getCouponsProperty()
    {
        if (!$this->gift || !$this->gift->coupon_ids) {
            return collect();
        }
        return Coupon::whereIn('id', $this->gift->coupon_ids)->get();
    }

    public function getCurrenciesProperty()
    {
        if (!$this->gift || !$this->gift->currency_codes) {
            return collect();
        }
        return \App\Models\Currency::whereIn('code', $this->gift->currency_codes)->get();
    }

    public function mount($code = null)
    {
        if ($code) {
            $this->code = strtoupper(trim($code));
            $this->autoRedeem = true;
            $this->checkCode();
        }
    }

    public function boot()
    {
        if ($this->autoRedeem && $this->gift) {
            $this->autoRedeem = false;
        }
    }

    public function updatedSelectedProductId($value)
    {
        $this->selectedPlanId = null;
    }

    public function getRedeemedGiftsProperty()
    {
        if (!Auth::check()) {
            return collect();
        }

        return \Paymenter\Extensions\Others\Gifts\Models\GiftRedemption::where('user_id', Auth::id())
            ->with(['gift', 'selectedService.product', 'selectedService.plan', 'selectedProduct', 'selectedPlan'])
            ->orderBy('redeemed_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('gifts::redeem')->layoutData([
            'sidebar' => true,
            'title' => 'Redeem Gift Code',
        ]);
    }
}
