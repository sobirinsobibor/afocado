<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class VariantOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'variantOptions';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grup Varian')
                    ->description('Contoh: "Pilihan Nasi", "Level Pedas", dll.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Grup Varian')
                            ->placeholder('Pilihan Nasi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Radio::make('selection_type')
                            ->label('Tipe Pilihan')
                            ->options([
                                'single' => 'Single (hanya bisa pilih 1)',
                                'multiple' => 'Multiple (bisa pilih lebih dari 1)',
                            ])
                            ->default('single')
                            ->inline()
                            ->required(),

                        Toggle::make('is_required')
                            ->label('Wajib Dipilih?')
                            ->helperText('Jika aktif, pelanggan wajib memilih salah satu opsi di grup ini.')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Grup Aktif')
                            ->default(true),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Repeater::make('children')
                    ->relationship('children')
                    ->label('Opsi / Sub-Menu')
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                        // isi pos_product_id child = product_id milik parent-nya
                        $data['pos_product_id'] = $record->pos_product_id;

                        return $data;
                    })
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Opsi')
                            ->placeholder('Nasi Putih Biasa')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),

                        TextInput::make('price')
                            ->label('Tambahan Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('Isi 0 jika gratis / tanpa tambahan.')
                            ->columnSpan(1),

                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(5)
                    ->addActionLabel('Tambah Opsi')
                    ->reorderable('sort_order')
                    ->orderColumn('sort_order')
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn ($query) => $query->whereNull('parent_id')) // hanya tampilkan parent
            ->columns([
                TextColumn::make('#')
                    ->rowIndex(),

                TextColumn::make('name')
                    ->label('Nama Grup Varian')
                    ->searchable(),

                TextColumn::make('selection_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'single' => 'Single',
                        'multiple' => 'Multiple',
                        default => '-',
                    })
                    ->color(fn (?string $state) => $state === 'multiple' ? 'info' : 'success'),

                IconColumn::make('is_required')
                    ->label('Wajib')
                    ->boolean(),

                TextColumn::make('children_count')
                    ->counts('children')
                    ->label('Jumlah Opsi')
                    ->badge()
                    ->alignCenter()
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),

                

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['parent_id'] = null; // dari header action selalu buat parent baru

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()->button()->hiddenLabel(),
                EditAction::make()->button()->hiddenLabel(),
                DeleteAction::make()->button()->hiddenLabel(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('sort_order');
    }
}