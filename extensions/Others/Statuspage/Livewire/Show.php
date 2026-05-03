<?php

namespace Paymenter\Extensions\Others\Statuspage\Livewire;

use Livewire\Component;
use Paymenter\Extensions\Others\Statuspage\Models\Incident;

class Show extends Component
{
    public Incident $incident;

    public function mount(Incident $incident)
    {
        if (!$incident) {
            abort(404);
        }

        $this->incident = $incident;
    }

    public function render()
    {
        return view('statuspage::show', [
            'incident' => $this->incident,
            'monitor' => $this->incident->monitor, 
        ]);
    }
}
