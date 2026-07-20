<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Pages;

use Modules\Pos\Filament\Resources\PosProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPosProducts extends ListRecords
{
    protected static string $resource = PosProductResource::class;

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Tambah')
                ->modalHeading('Tambah Menu Baru')
                ->modalWidth('2xl')
                ->createAnother(false)
                ->closeModalByClickingAway(false),
        ];
    }
}
