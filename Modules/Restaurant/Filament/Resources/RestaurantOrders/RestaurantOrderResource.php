<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantOrders;

use Modules\Restaurant\Filament\Resources\RestaurantOrders\Pages\CreateRestaurantOrder;
use Modules\Restaurant\Filament\Resources\RestaurantOrders\Pages\EditRestaurantOrder;
use Modules\Restaurant\Filament\Resources\RestaurantOrders\Pages\ListRestaurantOrders;
use Modules\Restaurant\Filament\Resources\RestaurantOrders\Schemas\RestaurantOrderForm;
use Modules\Restaurant\Filament\Resources\RestaurantOrders\Tables\RestaurantOrdersTable;
use Modules\Restaurant\Models\RestaurantOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RestaurantOrderResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false; 
    
    protected static ?string $model = RestaurantOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static string | UnitEnum | null $navigationGroup = 'Restaurant';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return RestaurantOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantOrders::route('/'),
            'create' => CreateRestaurantOrder::route('/create'),
            'edit' => EditRestaurantOrder::route('/{record}/edit'),
        ];
    }
}
