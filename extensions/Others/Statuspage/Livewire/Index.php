<?php

namespace Paymenter\Extensions\Others\Statuspage\Livewire;

use Livewire\Component;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Paymenter\Extensions\Others\Statuspage\Models\Incident;
use Paymenter\Extensions\Others\Statuspage\Models\Maintenance;
use Paymenter\Extensions\Others\Statuspage\Models\StatusPageSettings;

class Index extends Component
{
    public function mount()
    {
        if (Monitor::count() === 0 && Incident::count() === 0) {
            abort(404);
        }
    }

    public function render()
    {
        $settings = StatusPageSettings::getSettings();
        
        $categories = Monitor::getCategoryGroups();
        
        $incidents = collect();
        if ($settings->show_incidents ?? true) {
            $incidentsQuery = Incident::orderBy('started_at', 'desc');
            if ($settings->incidents_limit > 0) {
                $incidentsQuery->limit($settings->incidents_limit);
            }
            $incidents = $incidentsQuery->get();
        }

        $maintenances = collect();
        if ($settings->show_maintenance ?? true) {
            $maintenancesQuery = Maintenance::orderBy('started_at', 'desc');
            if ($settings->maintenance_limit > 0) {
                $maintenancesQuery->limit($settings->maintenance_limit);
            }
            $maintenances = $maintenancesQuery->get();
        }

        $activeMaintenanceQuery = Maintenance::where('status', '!=', 'completed')
            ->whereNotNull('started_at')
            ->where(function ($query) {
                $query->whereNull('completed_at')
                      ->orWhere('completed_at', '>', now());
            })
            ->orderBy('started_at', 'desc');
        
        if ($settings->maintenance_limit > 0) {
            $activeMaintenanceQuery->limit($settings->maintenance_limit);
        }
        
        $activeMaintenance = $activeMaintenanceQuery->first();

        $overall_status = 'all';
        $totalDown = 0;
        $total = 0;

        foreach ($categories as $category) {
            foreach ($category->monitors as $monitor) {
                $total++;
                if ($monitor->last_status !== 'up') {
                    $totalDown++;
                }
            }
        }

        if ($totalDown > 0 && $totalDown < $total) {
            $overall_status = 'partial';
        } elseif ($totalDown === $total && $total > 0) {
            $overall_status = 'major';
        }

        if ($activeMaintenance) {
            $overall_status = 'maintenance';
        }

        return view('statuspage::index', [
            'categories'     => $categories,
            'incidents'      => $incidents,
            'maintenances'   => $maintenances,
            'overall_status' => $overall_status,
            'active_maintenance' => $activeMaintenance,
            'settings' => $settings,
            '_nonce' => ''
        ]);
    }
}
