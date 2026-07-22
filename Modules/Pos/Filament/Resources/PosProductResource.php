<?php

namespace Modules\Pos\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Pos\Filament\Resources\PosProductResource\Pages\CreatePosProduct;
use Modules\Pos\Filament\Resources\PosProductResource\Pages\EditPosProduct;
use Modules\Pos\Filament\Resources\PosProductResource\Pages\ListPosProducts;
use Modules\Pos\Filament\Resources\PosProductResource\RelationManagers\VariantOptionsRelationManager;
use Modules\Pos\Filament\Resources\PosProductResource\Schemas\PosProductForm;
use Modules\Pos\Filament\Resources\PosProductResource\Schemas\PosProductInfolist;
use Modules\Pos\Filament\Resources\PosProductResource\Tables\PosProductsTable;
use Modules\Pos\Models\PosProduct;
use UnitEnum;

class PosProductResource extends Resource
{
    protected static ?string $model = PosProduct::class;

    protected static string | UnitEnum | null $navigationGroup = 'Point of Sale';

    protected static ?string $navigationLabel = 'Daftar Produk';

    protected static ?int $navigationSort = 4; 

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PosProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PosProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            VariantOptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosProducts::route('/'),
            'create' => CreatePosProduct::route('/create'),
            'edit' => EditPosProduct::route('/{record}/edit'),
        ];
    }
}
