<?php

namespace Modules\Pos\Filament\Resources\PosProductCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PosProductCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->rowIndex(),

                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(100)
                    ->searchable(),               
                
                TextColumn::make('products_count')
                    ->label('Jumlah Produk')
                    ->alignCenter()
                    ->counts('products')
                    ->badge(),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()->button()->hiddenLabel(),
                EditAction::make()->button()->hiddenLabel()
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
