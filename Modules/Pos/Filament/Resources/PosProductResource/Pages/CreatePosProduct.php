<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Pages;

use Modules\Pos\Filament\Resources\PosProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosProduct extends CreateRecord
{
    protected static string $resource = PosProductResource::class;

    //create another false
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
