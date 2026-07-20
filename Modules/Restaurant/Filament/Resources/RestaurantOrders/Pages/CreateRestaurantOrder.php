<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantOrders\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantOrders\RestaurantOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantOrder extends CreateRecord
{
    protected static string $resource = RestaurantOrderResource::class;
}
