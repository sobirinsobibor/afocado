<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Pos\Models\PosFoodcourtLocation;
use Modules\Pos\Models\PosProduct;
use Modules\Pos\Models\PosProductCategory;
use Modules\Pos\Models\PosTenant;
use Modules\Pos\Models\PosUnit;
use Rawilk\FilamentQuill\Enums\ToolbarButton;
use Rawilk\FilamentQuill\Filament\Forms\Components\QuillEditor; // pastikan ini yang dipakai

class PosProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        // Select::make('foodcourt_location_id')
                        //     ->label('Foodcourt Location')
                        //     ->options(fn () => PosFoodcourtLocation::query()->pluck('name', 'id'))
                        //     ->searchable()
                        //     ->live()
                        //     ->required()
                        //     ->dehydrated(false) // tidak disimpan ke pos_products, cuma buat filter
                        //     ->afterStateUpdated(fn (Set $set) => $set('tenant_id', null)),

                        // Select::make('tenant_id')
                        //     ->label('Tenant')
                        //     ->options(function (Get $get) {
                        //         $foodcourtId = $get('foodcourt_location_id');

                        //         if (! $foodcourtId) {
                        //             return [];
                        //         }

                        //         return PosTenant::query()
                        //             ->where('foodcourt_location_id', $foodcourtId)
                        //             ->pluck('name', 'id');
                        //     })
                        //     ->searchable()
                        //     ->required()
                        //     ->disabled(fn (Get $get) => ! $get('foodcourt_location_id')),

                        Select::make('foodcourt_location_id')
                            ->label('Foodcourt Location')
                            ->options(fn () => PosFoodcourtLocation::query()->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->required()
                            ->dehydrated(false) // tidak disimpan ke pos_products, cuma buat filter
                            ->afterStateHydrated(function (Set $set, Get $get, ?PosProduct $record) {
                                // saat edit, isi ulang berdasarkan tenant yang sudah tersimpan
                                if ($record && $record->tenant) {
                                    $set('foodcourt_location_id', $record->tenant->foodcourt_location_id);
                                }
                            })
                            ->afterStateUpdated(fn (Set $set) => $set('tenant_id', null)),

                        Select::make('tenant_id')
                            ->label('Tenant')
                            ->options(function (Get $get) {
                                $foodcourtId = $get('foodcourt_location_id');

                                if (! $foodcourtId) {
                                    return [];
                                }

                                return PosTenant::query()
                                    ->where('foodcourt_location_id', $foodcourtId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->disabled(fn (Get $get) => ! $get('foodcourt_location_id')),

                        TextInput::make('name')
                            ->label('Menu Makanan/Minuman')
                            ->required()
                            ->maxLength(255),

                        Select::make('pos_product_category_id')
                            ->label('Kategori Menu')
                            ->options(fn () => PosProductCategory::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('price_unit_id')
                            ->label('Satuan Harga')
                            ->options(fn () => PosUnit::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('stock')
                            ->label('Stok Tersedia')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Select::make('stock_unit_id')
                            ->label('Satuan Stok')
                            ->options(fn () => PosUnit::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Toggle::make('is_active')
                            ->required()
                            ->inline(false)
                            ->default(true),

                    
                        QuillEditor::make('description')
                            ->columnSpanFull()
                            ->disableToolbarButtons([
                                ToolbarButton::Font,
                                ToolbarButton::Size,
                                ToolbarButton::Indent,
                                ToolbarButton::Image,
                                ToolbarButton::Scripts,
                                ToolbarButton::TextAlign,
                                ToolbarButton::TextColor,
                                ToolbarButton::BackgroundColor,
                                ToolbarButton::Undo,
                                ToolbarButton::Redo,
                                ToolbarButton::ClearFormat,
                            ]),
                        
                        Radio::make('is_dine_in')
                            ->label('Melayani Makan di Tempat (Dine-In)')
                            ->boolean()
                            ->default(true)
                            ->inline()
                            ->required()
                            ->helperText('Jika "Tidak", tenant dianggap tutup untuk layanan makan di tempat.'),

                        Radio::make('is_take_away')
                            ->label('Melayani Bawa Pulang (Take Away)')
                            ->boolean()
                            ->default(true)
                            ->inline()
                            ->required()
                            ->helperText('Jika "Tidak", tenant dianggap tutup untuk layanan bawa pulang.'),


                        FileUpload::make('photos')
                            ->label('Photo')
                            ->multiple()
                            ->image()
                            ->maxFiles(3)
                            ->maxSize(3072)
                            ->directory('produk')
                            ->reorderable()
                            ->directory('menu')
                            ->panelLayout('grid')          // 👈 kuncinya di sini
                            ->imagePreviewHeight('200')  
                            ->columnSpanFull(),

                        
                        Toggle::make('has_variants')
                            ->required(),

                    ])
                    ->columns(2)
                    ->columnSpanFull()

            ]);
    }
}
