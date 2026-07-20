<?php

namespace Modules\Pos\Filament\Resources\PosTenants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Pos\Models\PosFoodcourtLocation;

class PosTenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                Select::make('foodcourt_location_id')
                    ->label('Foodcourt Location')
                    ->options(fn () => PosFoodcourtLocation::query()->pluck('name', 'id'))
                    ->searchable()
                    ->columnSpanFull()
                    ->required(),

                TextInput::make('name')
                    ->label('Nama Tenant')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),

                TextInput::make('owner_name')
                    ->label('Nama Pemilik')
                    ->columnSpanFull()
                    ->maxLength(255),

                FileUpload::make('photos')
                    ->label('Foto Tenant')
                    ->helperText('Maksimal 3 foto, ukuran masing-masing hingga 3 MB')                    
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1'])
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->maxFiles(3)
                    ->maxSize(3072)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('tenant')
                    ->multiple()
                    ->reorderable()
                    ->disk('local')
                    ->panelLayout('grid')          // 👈 kuncinya di sini
                    ->imagePreviewHeight('100')    // 👈 atur tinggi preview jadi lebih kecil
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
