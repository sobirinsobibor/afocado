<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                    ->searchable(),

                TextColumn::make('stock')
                    ->searchable(),

                TextColumn::make('stockUnit.name')
                    ->label('Unit Stok')
                    ->sortable(),

                TextColumn::make('priceUnit.name')
                    ->label('Unit Harga')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->searchable(),

                IconColumn::make('has_variants')
                    ->boolean(),

                TextColumn::make('tenant.name'),

                TextColumn::make('tenant.foodcourtLocation.name')
               
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
