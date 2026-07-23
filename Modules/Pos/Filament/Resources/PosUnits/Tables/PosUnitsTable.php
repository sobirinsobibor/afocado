<?php

namespace Modules\Pos\Filament\Resources\PosUnits\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PosUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex(),
                TextColumn::make('name')->label('Nama Unit')->searchable(),
                TextColumn::make('description')->label('Deskripsi')->limit(80),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make()->button()->hiddenLabel(),
            ]);
    }
}
