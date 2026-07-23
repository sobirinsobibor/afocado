<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantCategories;

use Modules\Restaurant\Filament\Resources\RestaurantCategories\Pages\CreateRestaurantCategory;
use Modules\Restaurant\Filament\Resources\RestaurantCategories\Pages\EditRestaurantCategory;
use Modules\Restaurant\Filament\Resources\RestaurantCategories\Pages\ListRestaurantCategories;
use Modules\Restaurant\Filament\Resources\RestaurantCategories\Schemas\RestaurantCategoryForm;
use Modules\Restaurant\Filament\Resources\RestaurantCategories\Tables\RestaurantCategoriesTable;
use Modules\Restaurant\Models\RestaurantCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RestaurantCategoryResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = RestaurantCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::NumberedList;

    protected static string | UnitEnum | null $navigationGroup = 'Restaurant';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RestaurantCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantCategoriesTable::configure($table);
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
            'index' => ListRestaurantCategories::route('/'),
            'create' => CreateRestaurantCategory::route('/create'),
            'edit' => EditRestaurantCategory::route('/{record}/edit'),
        ];
    }
}
