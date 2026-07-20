<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantCategories\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantCategories\RestaurantCategoryResource;  
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantCategory extends EditRecord
{
    protected static string $resource = RestaurantCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
