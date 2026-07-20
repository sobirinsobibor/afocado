<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\RestaurantMenuItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantMenuItems extends ListRecords
{
    protected static string $resource = RestaurantMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
