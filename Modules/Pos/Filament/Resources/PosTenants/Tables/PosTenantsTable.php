<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PosTenantsTable
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
                    ->label('Nama Tenant')
                    ->searchable(),

                TextColumn::make('foodcourtLocation.name')
                    ->label('Foodcourt')
                    ->searchable(),

                TextColumn::make('owner_name')
                    ->label('Pemilik')
                    ->searchable(),

                TextColumn::make('products_count')
                    ->label('Jumlah Produk')
                    ->counts('products')
                    ->alignCenter()
                    ->badge(),

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
