<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource;

class CreateMaintenance extends CreateRecord
{
    protected static string $resource = MaintenanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = 'maintenance-' . uniqid();
        }
        return $data;
    }
}
