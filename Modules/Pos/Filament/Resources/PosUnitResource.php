<?php

namespace Modules\Pos\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Pos\Filament\Resources\PosUnits\Pages\CreatePosUnit;
use Modules\Pos\Filament\Resources\PosUnits\Pages\EditPosUnit;
use Modules\Pos\Filament\Resources\PosUnits\Pages\ListPosUnits;
use Modules\Pos\Filament\Resources\PosUnits\Schemas\PosUnitForm;
use Modules\Pos\Filament\Resources\PosUnits\Tables\PosUnitsTable;
use Modules\Pos\Models\PosUnit;
use UnitEnum;

class PosUnitResource extends Resource
{
    protected static ?string $model = PosUnit::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Unit Produk';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Schema $schema): Schema
    {
        return PosUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosUnits::route('/'),
            // 'create' => CreatePosUnit::route('/create'),
            // 'edit' => EditPosUnit::route('/{record}/edit'),
        ];
    }
}
