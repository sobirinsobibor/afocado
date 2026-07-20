<?php

namespace Modules\Pos\Filament\Resources\PosProductResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Pos\Models\PosVariantType;

class VariantOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'variantOptions';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pos_variant_type_id')
                    ->label('Variant Type')
                    ->options(function () {
                        return PosVariantType::where('pos_product_id', $this->ownerRecord->id)
                            ->orderBy('position')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('variantType.name')
                    ->label('Variant Type')
                    ->sortable()
                    ->placeholder('No variant type assigned'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('productSkusAsOption1_count')
                    ->counts('productSkusAsOption1')
                    ->label('Used as Option 1')
                    ->badge()
                    ->color('success'),
                TextColumn::make('productSkusAsOption2_count')
                    ->counts('productSkusAsOption2')
                    ->label('Used as Option 2')
                    ->badge()
                    ->color('info'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}