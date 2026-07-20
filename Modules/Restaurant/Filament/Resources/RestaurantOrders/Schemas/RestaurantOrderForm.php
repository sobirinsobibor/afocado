<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantOrders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Modules\Restaurant\Models\RestaurantMenuItem;

class RestaurantOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1) // Tambahkan ini untuk full width
            ->components([
                Section::make('Informasi Order')
                    ->schema([
                        Select::make('restaurant_table_id')
                            ->label('Nomor Meja')
                            ->relationship('restaurantTable', 'table_number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        TextInput::make('order_number')
                            ->label('No. Order')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'ORD-' . strtoupper(uniqid()))
                            ->readOnly(),
                        
                        TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->maxLength(255),
                        
                        Select::make('status')
                            ->label('Status Order')
                            ->options([
                                'active' => 'Aktif',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false)
                            ->live(),
                        
                        DateTimePicker::make('completed_at')
                            ->label('Tanggal Selesai')
                            ->visible(fn(Get $get) => in_array($get('status'), ['completed', 'cancelled'])),
                    ])
                    ->columns(4) // Layout 4 kolom untuk section ini
                    ->collapsible(),

                Section::make('Item Pesanan')
                    ->schema([
                        Repeater::make('orderItems')
                            ->relationship()
                            ->schema([
                                Grid::make(12) // Gunakan 12 kolom untuk lebih flexible
                                    ->schema([
                                        Select::make('menu_item_id')
                                            ->label('Menu')
                                            ->relationship('menuItem', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $menuItem = RestaurantMenuItem::find($state);
                                                    if ($menuItem) {
                                                        $set('price', $menuItem->price);
                                                        $quantity = $get('quantity') ?? 1;
                                                        $set('subtotal', $menuItem->price * $quantity);
                                                    }
                                                }
                                            })
                                            ->columnSpan(4),

                                        TextInput::make('quantity')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $price = $get('price') ?? 0;
                                                $set('subtotal', $price * ($state ?? 1));
                                            })
                                            ->columnSpan(1),

                                        TextInput::make('price')
                                            ->label('Harga')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required()
                                            ->readOnly()
                                            ->columnSpan(2),

                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->readOnly()
                                            ->columnSpan(2),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'preparing' => 'Diproses',
                                                'ready' => 'Siap',
                                                'served' => 'Disajikan',
                                            ])
                                            ->default('pending')
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(3),
                                    ]),

                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['menu_item_id']) && $state['menu_item_id'] 
                                    ? RestaurantMenuItem::find($state['menu_item_id'])?->name 
                                    : 'Item Baru'
                            )
                            ->addActionLabel('Tambah Menu')
                            ->defaultItems(0)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->deleteAction(
                                fn ($action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                            )
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull() // Full width untuk section
                    ->collapsible(),

                Section::make('Ringkasan & Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_amount')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->required(),

                                TextInput::make('tax')
                                    ->label('Pajak (10%)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->required(),

                                TextInput::make('grand_total')
                                    ->label('Grand Total')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->required()
                                    ->extraAttributes(['class' => 'font-bold text-lg']),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        'unpaid' => 'Belum Dibayar',
                                        'paid' => 'Sudah Dibayar',
                                    ])
                                    ->required()
                                    ->default('unpaid')
                                    ->native(false)
                                    ->live(),

                                Select::make('payment_method')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'cash' => 'Tunai',
                                        'card' => 'Kartu',
                                        'qris' => 'QRIS',
                                        'e-wallet' => 'E-Wallet',
                                    ])
                                    ->native(false)
                                    ->visible(fn(Get $get) => $get('payment_status') === 'paid')
                                    ->required(fn(Get $get) => $get('payment_status') === 'paid'),
                            ]),

                        Textarea::make('notes')
                            ->label('Catatan Order')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    protected static function updateTotals(Get $get, Set $set): void
    {
        $orderItems = collect($get('orderItems') ?? []);
        
        $total = $orderItems->sum(function ($item) {
            return floatval($item['subtotal'] ?? 0);
        });

        $tax = $total * 0.11; // 10% tax
        $grandTotal = $total + $tax;

        $set('total_amount', round($total, 2));
        $set('tax', round($tax, 2));
        $set('grand_total', round($grandTotal, 2));
    }
}