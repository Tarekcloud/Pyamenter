<?php

namespace Paymenter\Extensions\Others\ReferralSystem\Listeners;

use App\Events\Order\Created;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Paymenter\Extensions\Others\ReferralSystem\Models\ReferralCode;
use Paymenter\Extensions\Others\ReferralSystem\Models\ReferralOrder;

class AssociateOrderWithReferral
{
    public function handle(Created $event): void
    {
        $referralCode = Cookie::get('referral_code');

        if (!$referralCode) {
            return;
        }

        $code = ReferralCode::query()
            ->whereRaw('LOWER(code) = ?', [Str::lower($referralCode)])
            ->first();

        if (!$code || !$code->isActive()) {
            return;
        }

        // Don't allow self-referral 548fdcc170237119fce11c345ee45c29
        if ($code->user_id === $event->order->user_id) {
            return;
        }

        // Check if association already exists 548fdcc170237119fce11c345ee45c29
        if (ReferralOrder::where('order_id', $event->order->id)->exists()) {
            return;
        }

        ReferralOrder::create([
            'order_id' => $event->order->id,
            'referral_code_id' => $code->id,
        ]);
    }
}
