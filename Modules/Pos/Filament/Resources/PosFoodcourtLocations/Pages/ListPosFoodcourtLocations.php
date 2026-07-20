<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Pos\Filament\Resources\PosFoodcourtLocationResource;

class ListPosFoodcourtLocations extends ListRecords
{
    protected static string $resource = PosFoodcourtLocationResource::class;

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Tambah')
                ->modalHeading('Tambah Foodcourt Baru')
                ->modalWidth('2xl')
                ->createAnother(false)
                ->closeModalByClickingAway(false),
        ];
    }
}
