<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantTables\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Pos\Models\PosFoodcourtLocation;
use Modules\Restaurant\Models\RestaurantTable;

class RestaurantTableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->schema([
                        Select::make('foodcourt_location_id')
                            ->label('Foodcourt')
                            ->options(fn () => PosFoodcourtLocation::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'table' => 'Meja',
                                'counter' => 'Konter',
                                'pickup' => 'Titik Ambil (Pickup)',
                                'delivery' => 'Delivery',
                            ])
                            ->default('table')
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                // hanya auto-generate saat create (belum ada table_number manual)
                                if (blank($get('table_number'))) {
                                    $set('table_number', self::generateTableNumber($state));
                                }
                            }),

                        TextInput::make('name')
                            ->label('Nama')
                            ->placeholder('Meja 1 / Konter Utama / Pickup Point A')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('table_number')
                            ->label('Kode')
                            ->placeholder('TBL-001')
                            ->required()
                            ->hidden()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->helperText('Kode unik untuk identifikasi meja/konter, otomatis terisi saat memilih tipe (bisa diedit manual).'),

                        TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('orang')
                            ->visible(fn (Get $get) => $get('type') === 'table')
                            ->helperText('Khusus tipe Meja, jumlah kursi/kapasitas.'),

                        TextInput::make('location')
                            ->label('Lokasi Fisik')
                            ->placeholder('Lantai 1 dekat pintu masuk')
                            ->maxLength(255),
                        
                        Textarea::make('description')
                            ->label('Catatan Tambahan')
                            ->placeholder('Contoh: dekat area smoking, cocok untuk grup besar, dll.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Status & Ketersediaan')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'available' => 'Tersedia',
                                'occupied' => 'Terisi',
                                'reserved' => 'Direservasi',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('available')
                            ->required(),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Jika nonaktif, meja/konter ini tidak akan muncul di self-order.'),
                    ])
                    ->columns(3),
            ]);
    }

    protected static function generateTableNumber(?string $type): string
    {
        $prefix = match ($type) {
            'table' => 'TBL',
            'counter' => 'CNT',
            'pickup' => 'PKP',
            'delivery' => 'DLV',
            default => 'TBL',
        };

        $lastNumber = RestaurantTable::query()
            ->where('table_number', 'like', $prefix . '-%')
            ->orderByDesc('id')
            ->value('table_number');

        $nextSequence = $lastNumber
            ? ((int) substr($lastNumber, strrpos($lastNumber, '-') + 1)) + 1
            : 1;

        return sprintf('%s-%03d', $prefix, $nextSequence);
    }
}