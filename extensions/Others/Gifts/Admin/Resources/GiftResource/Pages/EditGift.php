<?php

namespace Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource;

class EditGift extends EditRecord
{
    protected static string $resource = GiftResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['code']) && !empty($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        return $data;
    }
}
