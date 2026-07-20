<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PosFoodcourtLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                TextInput::make('name')
                    ->label('Nama Foodcourt')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Foto Lokasi')
                    ->helperText('Maksimal 3 foto, ukuran masing-masing hingga 3 MB')                    
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1'])
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->maxFiles(3)
                    ->maxSize(3072)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('foodcourt')
                    ->multiple()
                    ->reorderable()
                    ->disk('local')
                    ->panelLayout('grid')          // 👈 kuncinya di sini
                    ->imagePreviewHeight('100')    // 👈 atur tinggi preview jadi lebih kecil
                    ->columnSpanFull(),

                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

            ]);
    }
}
