<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources\KnowledgeBaseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\KnowledgeBaseResource;

class EditKnowledgeBase extends EditRecord
{
    protected static string $resource = KnowledgeBaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
