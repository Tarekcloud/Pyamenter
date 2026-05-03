<?php

namespace Paymenter\Extensions\Others\Gifts\Livewire\Gifts;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\Gifts\Models\Gift;

class RedeemDirect extends Component
{
    public $code;
    public $gift = null;

    public function mount($code)
    {
        $this->code = strtoupper(trim($code));
        $this->gift = Gift::where('code', $this->code)->first();
        
        if (!Auth::check()) {
            session()->put('url.intended', route('gifts.redeem.direct', ['code' => $code]));
            return redirect()->route('login');
        }

        return redirect()->route('gifts.redeem.code', ['code' => $code]);
    }

    public function render()
    {
        $giftName = $this->gift ? ($this->gift->description ?: 'Gift') : 'Gift';
        $giftDescription = $this->gift ? ($this->gift->description ?: 'You received a gift! Redeem it now.') : 'You received a gift! Redeem it now.';
        
        return view('gifts::redeem-direct')->layoutData([
            'sidebar' => true,
            'title' => "You Got a Gift! - {$giftName}",
        ])->with([
            'gift' => $this->gift,
            'giftName' => $giftName,
            'giftDescription' => $giftDescription,
        ]);
    }
}
