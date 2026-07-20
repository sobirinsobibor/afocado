<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Pos\Filament\Resources\PosFoodcourtLocationResource;

class EditPosFoodcourtLocation extends EditRecord
{
    protected static string $resource = PosFoodcourtLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
