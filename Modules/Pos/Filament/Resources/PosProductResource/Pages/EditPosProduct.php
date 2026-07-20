<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Pages;

use Modules\Pos\Filament\Resources\PosProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPosProduct extends EditRecord
{
    protected static string $resource = PosProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
