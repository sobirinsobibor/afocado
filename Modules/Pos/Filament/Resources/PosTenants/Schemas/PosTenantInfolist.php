<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PosTenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('photos')
                    ->label('photo')
                    ->square()
                    ->simpleLightbox("Your Url address")
                    ->columnSpanFull(),

                TextEntry::make('foodcourtLocation.name')
                    ->label('Foodcourt')
                    ->columnSpanFull(),

                TextEntry::make('name')
                    ->label('Nama Tenant')
                    ->columnSpanFull(),

                TextEntry::make('owner_name')
                    ->label('Pemilik')
                    ->columnSpanFull(),

                TextEntry::make('products_count')
                    ->label('Jumlah Produk')
                    ->state(fn ($record) => $record->products()->count()),
            ]);
    }
}
