<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Pos\Filament\Resources\PosTenantResource;

class CreatePosTenant extends CreateRecord
{
    protected static string $resource = PosTenantResource::class;
}
