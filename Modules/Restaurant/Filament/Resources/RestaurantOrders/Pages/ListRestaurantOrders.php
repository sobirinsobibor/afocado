<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantOrders\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantOrders\RestaurantOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantOrders extends ListRecords
{
    protected static string $resource = RestaurantOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
