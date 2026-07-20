<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Modules\Pos\Filament\Resources\PosFoodcourtLocationResource;

class CreatePosFoodcourtLocation extends CreateRecord
{
    protected static string $resource = PosFoodcourtLocationResource::class;

    //create another false
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

   
        
}
