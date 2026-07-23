<?php

namespace Modules\Pos\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Pos\Filament\Resources\PosTenants\Pages\CreatePosTenant;
use Modules\Pos\Filament\Resources\PosTenants\Pages\EditPosTenant;
use Modules\Pos\Filament\Resources\PosTenants\Pages\ListPosTenants;
use Modules\Pos\Filament\Resources\PosTenants\Pages\ViewPosTenant;
use Modules\Pos\Filament\Resources\PosTenants\Schemas\PosTenantForm;
use Modules\Pos\Filament\Resources\PosTenants\Schemas\PosTenantInfolist;
use Modules\Pos\Filament\Resources\PosTenants\Tables\PosTenantsTable;
use Modules\Pos\Models\PosTenant;
use UnitEnum;

class PosTenantResource extends Resource
{
    protected static ?string $model = PosTenant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'Tenant';

    protected static ?string $navigationLabel = 'Daftar Tenant';

    protected static ?int $navigationSort = 2; 


    public static function form(Schema $schema): Schema
    {
        return PosTenantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PosTenantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosTenantsTable::configure($table);
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
            'index' => ListPosTenants::route('/'),
            // 'create' => CreatePosTenant::route('/create'),
            // 'view' => ViewPosTenant::route('/{record}'),
            // 'edit' => EditPosTenant::route('/{record}/edit'),
        ];
    }
}
