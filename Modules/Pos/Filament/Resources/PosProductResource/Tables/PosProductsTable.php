<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PosProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->rowIndex(),

                ImageColumn::make('photos')
                    ->disk('local')
                    ->square()
                    ->simpleLightbox("Your Url address"),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => 'Rp' . number_format($state, 0, ',', '.') . '/' . ($record->priceUnit?->name ?? '')),
                
               TextColumn::make('stock')
                    ->label('Stok')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => number_format($state, 0, ',', '.') . ' ' . ($record->stockUnit?->name ?? '')),


                TextColumn::make('category.name')
                    ->searchable(),

                ToggleColumn::make('has_variants')
                    ->label('Variant'),

                ToggleColumn::make('is_dine_in')
                    ->label('Dine In'),

                
                ToggleColumn::make('is_take_away')
                    ->label('Take Away'),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->description(fn ($record) => $record->tenant?->foodcourtLocation?->name ?? ''),  
                    
                ToggleColumn::make('is_active')
                    ->label('Is Active'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->button()->hiddenLabel(),
                EditAction::make()->button()->hiddenLabel(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
