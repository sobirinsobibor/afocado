<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Pos\Filament\Resources\PosFoodcourtLocationResource;

class ViewPosFoodcourtLocation extends ViewRecord
{
    protected static string $resource = PosFoodcourtLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
