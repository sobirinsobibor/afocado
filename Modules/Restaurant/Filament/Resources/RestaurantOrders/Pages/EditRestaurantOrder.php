<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantOrders\Pages;

use Modules\Restaurant\Filament\Resources\RestaurantOrders\RestaurantOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantOrder extends EditRecord
{
    protected static string $resource = RestaurantOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
