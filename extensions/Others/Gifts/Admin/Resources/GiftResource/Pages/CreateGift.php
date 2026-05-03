<?php

namespace Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource;
use Paymenter\Extensions\Others\Gifts\Models\Gift;

class CreateGift extends CreateRecord
{
    protected static string $resource = GiftResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['code']) || trim($data['code']) === '') {
            $data['code'] = $this->generateCode();
        } else {
            $data['code'] = strtoupper(trim($data['code']));
        }

        return $data;
    }

    protected function generateCode(int $length = 12): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        while (Gift::where('code', $code)->exists()) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        }
        
        return $code;
    }
}
