<?php

namespace Modules\Restaurant\Filament\Resources\RestaurantTables\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\Restaurant\Models\RestaurantTable;

class RestaurantTablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->rowIndex(),

                ImageColumn::make('qr_code_path')
                    ->disk('local')
                    ->square()
                    ->simpleLightbox("Your Url address"),

                TextColumn::make('table_number')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->location),

                TextColumn::make('foodcourtLocation.name')
                    ->label('Foodcourt')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'table' => 'Meja',
                        'counter' => 'Konter',
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'table' => 'success',
                        'counter' => 'info',
                        'pickup' => 'warning',
                        'delivery' => 'purple',
                        default => 'gray',
                    }),

                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->placeholder('-')
                    ->suffix(' orang'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'reserved' => 'Direservasi',
                        'inactive' => 'Nonaktif',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        'reserved' => 'warning',
                        'inactive' => 'gray',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('foodcourt_location_id')
                    ->label('Foodcourt')
                    ->relationship('foodcourtLocation', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'table' => 'Meja',
                        'counter' => 'Konter',
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'reserved' => 'Direservasi',
                        'inactive' => 'Nonaktif',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                ViewAction::make()->button()->hiddenLabel(),
                EditAction::make()->button()->hiddenLabel(),
                Action::make('generateQr')
                    ->label('Generate QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->action(function (RestaurantTable $record) {
                        $record->generateQrCode();

                        Notification::make()
                            ->title('QR Code berhasil dibuat')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('sort_order');
    }
}