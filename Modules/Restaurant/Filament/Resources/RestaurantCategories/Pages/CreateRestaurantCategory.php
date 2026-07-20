<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantCategories\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantCategories\RestaurantCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantCategory extends CreateRecord
{
    protected static string $resource = RestaurantCategoryResource::class;
}
