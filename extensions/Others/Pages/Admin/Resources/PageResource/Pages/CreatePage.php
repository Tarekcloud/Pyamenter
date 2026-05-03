<?php

namespace Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource\Pages;

use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
