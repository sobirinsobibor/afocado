<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class PosFoodcourtLocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->rowIndex(),

                ImageColumn::make('photos')
                    ->square()
                    ->disk('local')
                    ->simpleLightbox(),

                TextColumn::make('name')
                    ->label('Nama Foodcourt')
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50),

                TextColumn::make('tenants_count')
                    ->label('Jumlah Tenant')
                    ->counts('tenants')
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
