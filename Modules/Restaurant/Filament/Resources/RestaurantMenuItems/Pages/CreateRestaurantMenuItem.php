<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\RestaurantMenuItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantMenuItem extends CreateRecord
{
    protected static string $resource = RestaurantMenuItemResource::class;
}
