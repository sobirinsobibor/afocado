<?php

namespace Modules\Pos\Filament\Resources\PosUnits\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PosUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex(),
                TextColumn::make('name')->label('Nama Unit')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('type')->label('Tipe')->searchable(),
                TextColumn::make('description')->label('Deskripsi')->limit(80),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->button()->hiddenLabel(),
            ]);
    }
}
