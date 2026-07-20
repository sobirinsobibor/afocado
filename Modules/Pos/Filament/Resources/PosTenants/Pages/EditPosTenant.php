<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Pos\Filament\Resources\PosTenantResource;

class EditPosTenant extends EditRecord
{
    protected static string $resource = PosTenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
