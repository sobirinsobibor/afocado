<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Pos\Filament\Resources\PosTenantResource;

class ListPosTenants extends ListRecords
{
    protected static string $resource = PosTenantResource::class;

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
