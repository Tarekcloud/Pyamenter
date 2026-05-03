<?php

namespace Paymenter\Extensions\Others\Statuspage\Livewire;

use Livewire\Component;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Paymenter\Extensions\Others\Statuspage\Models\Incident;

class Widget extends Component
{
    public function mount()
    {
        if (Monitor::count() === 0 && Incident::count() === 0) {
            abort(404);
        }
    }

    public function render()
    {
        return view('statuspage::widget', [
            'monitors'  => Monitor::all(),
            'incidents' => Incident::orderBy('started_at', 'desc')
                                   ->take(3)
                                   ->get(),
            'sig' => ''
        ]);
    }
}
