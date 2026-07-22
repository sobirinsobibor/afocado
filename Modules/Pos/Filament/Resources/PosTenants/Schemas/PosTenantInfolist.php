<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class PosTenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        // KIRI: foto
                        ImageEntry::make('photos')
                            ->label('Photo')
                            ->square()
                            ->simpleLightbox("Your Url address")
                            ->columnSpan(1),

                        // KANAN: informasi
                        Group::make([
                            TextEntry::make('foodcourtLocation.name')
                                ->label('Foodcourt')
                                ->columnSpanFull(),

                            TextEntry::make('name')
                                ->label('Nama Tenant')
                                ->columnSpanFull(),

                            TextEntry::make('owner_name')
                                ->label('Pemilik')
                                ->columnSpanFull(),

                            TextEntry::make('owner_phone_number')
                                ->label('Kontak Pemilik')
                                ->formatStateUsing(fn (?string $state) => filled($state) ? '+' . $state : '-')
                                ->columnSpanFull(),

                            TextEntry::make('lokasi_tenant')
                                ->label('Nomor/Blok Tenant')
                                ->columnSpanFull(),

                            TextEntry::make('products_count')
                                ->label('Jumlah Produk')
                                ->state(fn ($record) => $record->products()->count())
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(1),
                    ]),
            ]);
    }
}
