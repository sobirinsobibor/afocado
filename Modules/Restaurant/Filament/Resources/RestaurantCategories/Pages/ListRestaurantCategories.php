<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantCategories\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantCategories\RestaurantCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantCategories extends ListRecords
{
    protected static string $resource = RestaurantCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
