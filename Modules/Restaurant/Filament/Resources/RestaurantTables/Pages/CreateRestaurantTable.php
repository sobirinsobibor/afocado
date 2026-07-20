<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantTables\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantTables\RestaurantTableResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantTable extends CreateRecord
{
    protected static string $resource = RestaurantTableResource::class;
}
