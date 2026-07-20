<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Njxqlus\Filament\Components\Infolists\LightboxImageEntry;

class PosFoodcourtLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('photos')
                    ->label('photo')
                    ->disk('local')
                    ->square()
                    ->simpleLightbox('test')
                    ->columnSpanFull(),
               
                TextEntry::make('name')->label('Nama Foodcourt')->columnSpanFull(),
                TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                TextEntry::make('tenants_count')
                    ->label('Jumlah Tenant')
                    ->columnSpanFull()
                    ->state(fn ($record) => $record->tenants()->count()),
            ]);
    }
}
