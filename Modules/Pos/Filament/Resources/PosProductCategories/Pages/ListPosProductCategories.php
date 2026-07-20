<?php

namespace Modules\Pos\Filament\Resources\PosProductCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Pos\Filament\Resources\PosProductCategoryResource;

class ListPosProductCategories extends ListRecords
{
    protected static string $resource = PosProductCategoryResource::class;

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
