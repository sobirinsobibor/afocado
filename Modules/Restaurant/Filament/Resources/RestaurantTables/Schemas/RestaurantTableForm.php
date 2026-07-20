<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantTables\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class RestaurantTableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('table_number')
                            ->label('Nomor Meja')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: 01, A1, VIP-1'),

                TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(20)
                    ->suffix('orang')
                    ->placeholder('Jumlah orang'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'reserved' => 'Direservasi',
                    ])
                    ->required()
                    ->default('available')
                    ->native(false),

                TextInput::make('qr_code')
                    ->label('QR Code')
                    ->maxLength(255)
                    ->placeholder('Kode QR (opsional)')
                    ->helperText('QR Code untuk akses cepat meja ini'),
            ]);
    }
}