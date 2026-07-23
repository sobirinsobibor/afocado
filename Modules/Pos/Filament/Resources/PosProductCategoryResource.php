<?php

namespace Modules\Pos\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Pos\Filament\Resources\PosProductCategories\Pages\CreatePosProductCategory;
use Modules\Pos\Filament\Resources\PosProductCategories\Pages\EditPosProductCategory;
use Modules\Pos\Filament\Resources\PosProductCategories\Pages\ListPosProductCategories;
use Modules\Pos\Filament\Resources\PosProductCategories\Schemas\PosProductCategoryForm;
use Modules\Pos\Filament\Resources\PosProductCategories\Tables\PosProductCategoriesTable;
use Modules\Pos\Models\PosProductCategory;
use UnitEnum;

class PosProductCategoryResource extends Resource
{
    protected static ?string $model = PosProductCategory::class;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PosProductCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosProductCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosProductCategories::route('/'),
            // 'create' => CreatePosProductCategory::route('/create'),
            // 'edit' => EditPosProductCategory::route('/{record}/edit'),
        ];
    }
}
