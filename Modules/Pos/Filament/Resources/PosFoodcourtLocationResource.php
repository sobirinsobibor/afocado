<?php

namespace Modules\Pos\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages\CreatePosFoodcourtLocation;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages\EditPosFoodcourtLocation;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages\ListPosFoodcourtLocations;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Pages\ViewPosFoodcourtLocation;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Schemas\PosFoodcourtLocationForm;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Schemas\PosFoodcourtLocationInfolist;
use Modules\Pos\Filament\Resources\PosFoodcourtLocations\Tables\PosFoodcourtLocationsTable;
use Modules\Pos\Models\PosFoodcourtLocation;

class PosFoodcourtLocationResource extends Resource
{
    protected static ?string $model = PosFoodcourtLocation::class;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Daftar Foodcourt';

    protected static ?string $recordTitleAttribute = 'Food Court';

    protected static ?int $navigationSort = 1; 

    public static function form(Schema $schema): Schema
    {
        return PosFoodcourtLocationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PosFoodcourtLocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosFoodcourtLocationsTable::configure($table);
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
            'index' => ListPosFoodcourtLocations::route('/'),
            // 'create' => CreatePosFoodcourtLocation::route('/create'),
            // 'view' => ViewPosFoodcourtLocation::route('/{record}'),
            // 'edit' => EditPosFoodcourtLocation::route('/{record}/edit'),
        ];
    }
}
