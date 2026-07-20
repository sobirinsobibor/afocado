<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantTables;

use Modules\Restaurant\Filament\Resources\RestaurantTables\Pages\CreateRestaurantTable;
use Modules\Restaurant\Filament\Resources\RestaurantTables\Pages\EditRestaurantTable;
use Modules\Restaurant\Filament\Resources\RestaurantTables\Pages\ListRestaurantTables;
use Modules\Restaurant\Filament\Resources\RestaurantTables\Schemas\RestaurantTableForm;
use Modules\Restaurant\Filament\Resources\RestaurantTables\Tables\RestaurantTablesTable;
use Modules\Restaurant\Models\RestaurantTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;



class RestaurantTableResource extends Resource
{
    protected static ?string $model = RestaurantTable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Restaurant';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return RestaurantTableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantTablesTable::configure($table);
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
            'index' => ListRestaurantTables::route('/'),
            'create' => CreateRestaurantTable::route('/create'),
            'edit' => EditRestaurantTable::route('/{record}/edit'),
        ];
    }
}
