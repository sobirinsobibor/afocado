<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\RestaurantMenuItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantMenuItem extends EditRecord
{
    protected static string $resource = RestaurantMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
