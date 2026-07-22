<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class PosProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Group::make([
                    // Kolom Kiri - Gambar
                    ImageEntry::make('photos')
                        ->label('Photo')
                        ->square()
                        ->simpleLightbox('Your Url address'),
                ]),

                // Kolom Kanan - Informasi
                Group::make([
                    TextEntry::make('tenant.name')
                        ->label('Tenant')
                        ->columnSpanFull(),

                    TextEntry::make('name')
                        ->label('Menu Makanan/Minuman')
                        ->columnSpanFull(),

                    TextEntry::make('category.name')
                        ->label('Kategori Menu')
                        ->columnSpanFull(),

                    TextEntry::make('price')
                        ->label('Harga')
                        ->formatStateUsing(function ($state, $record) {
                            $formatted = 'Rp' . number_format($state, 0, ',', '.');

                            return $record->priceUnit?->name
                                ? "{$formatted} / {$record->priceUnit->name}"
                                : $formatted;
                        })
                        ->columnSpanFull(),

                    TextEntry::make('stock')
                        ->label('Stok Tersedia')
                        ->formatStateUsing(function ($state, $record) {
                            $formatted = number_format($state, 0, ',', '.');

                            return $record->stockUnit?->name
                                ? "{$formatted} {$record->stockUnit->name}"
                                : $formatted;
                        })
                        ->columnSpanFull(),

                    IconEntry::make('is_active')
                        ->label('Status Aktif')
                        ->boolean()
                        ->columnSpanFull(),

                    IconEntry::make('is_dine_in')
                        ->label('Dine-In')
                        ->boolean()
                        ->columnSpanFull(),

                    IconEntry::make('is_take_away')
                        ->label('Take Away')
                        ->boolean()
                        ->columnSpanFull(),

                    IconEntry::make('has_variants')
                        ->label('Punya Varian')
                        ->boolean()
                        ->columnSpanFull(),

                        // Deskripsi - Full Width
                    TextEntry::make('description')
                        ->label('Deskripsi')
                        ->html()
                ])
            ]);
    }
}