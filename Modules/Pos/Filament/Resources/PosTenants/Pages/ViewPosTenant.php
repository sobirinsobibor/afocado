<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Pos\Filament\Resources\PosTenantResource;

class ViewPosTenant extends ViewRecord
{
    protected static string $resource = PosTenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
