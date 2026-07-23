<?php

namespace Modules\Pos\Filament\Resources\PosUnits\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Pos\Filament\Resources\PosUnitResource;

class ListPosUnits extends ListRecords
{
    protected static string $resource = PosUnitResource::class;

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Tambah')
                ->modalHeading('Tambah Tenant Baru')
                ->modalWidth('2xl')
                ->createAnother(false)
                ->closeModalByClickingAway(false),
        ];
    }
}
