<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Pos\Models\PosFoodcourtLocation;
use Modules\Pos\Models\PosProductCategory;
use Modules\Pos\Models\PosTenant;
use Modules\Pos\Models\PosUnit;

class PosProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('foodcourt_location_id')
                    ->label('Foodcourt Location')
                    ->options(fn () => PosFoodcourtLocation::query()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->required()
                    ->dehydrated(false) // tidak disimpan ke pos_products, cuma buat filter
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
                    ->required()
                    ->maxLength(255),

                Select::make('pos_product_category_id')
                    ->label('Kategori Produk')
                    ->options(fn () => PosProductCategory::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->helperText('Sesuaikan harga dengan harga harian yang berlaku.'),

                Select::make('price_unit_id')
                    ->label('Unit Harga')
                    ->options(fn () => PosUnit::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Masukkan stok sesuai ketersediaan harian.'),

                Select::make('stock_unit_id')
                    ->label('Unit Stok')
                    ->options(fn () => PosUnit::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Toggle::make('is_active')
                    ->required()
                    ->inline(false)
                    ->default(true),

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
                    ->imagePreviewHeight('100')  
                    ->columnSpanFull(),

                RichEditor::make('description')
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 20rem; max-height: 50vh; overflow-y: auto;'])
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'bulletList',
                        'orderedList',
                        'link',
                        'codeBlock',
                        'blockquote',
                    ]),

                Toggle::make('has_variants')
                    ->required(),
                    
            ]);
    }
}
