<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantMenuItems;

use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Pages\CreateRestaurantMenuItem;
use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Pages\EditRestaurantMenuItem;
use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Pages\ListRestaurantMenuItems;
use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Schemas\RestaurantMenuItemForm;
use Modules\Restaurant\Filament\Resources\RestaurantMenuItems\Tables\RestaurantMenuItemsTable;
use Modules\Restaurant\Models\RestaurantMenuItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RestaurantMenuItemResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = RestaurantMenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QueueList;

    protected static string | UnitEnum | null $navigationGroup = 'Restaurant';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return RestaurantMenuItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantMenuItemsTable::configure($table);
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
            'index' => ListRestaurantMenuItems::route('/'),
            'create' => CreateRestaurantMenuItem::route('/create'),
            'edit' => EditRestaurantMenuItem::route('/{record}/edit'),
        ];
    }
}
