<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource;

class EditMaintenance extends EditRecord
{
    protected static string $resource = MaintenanceResource::class;

    protected function afterSave(): void
    {
        $maintenance = $this->record;
        $lastUpdate = $maintenance->updates()->latest()->first();
        
        if ($lastUpdate && $lastUpdate->status !== $maintenance->status) {
            $maintenance->update(['status' => $lastUpdate->status]);
            
            if ($lastUpdate->status === 'completed') {
                $maintenance->update(['completed_at' => now()]);
            }
        }
    }
}
