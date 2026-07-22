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
                    ->maxLength(255),

                TextInput::make('owner_phone_number')
                    ->label('Kontak Pemilik')
                    ->tel()
                    ->prefix('+62')
                    ->placeholder('81234567890')
                    ->maxLength(15)
                    ->dehydrateStateUsing(function (?string $state) {
                        if (blank($state)) {
                            return $state;
                        }

                        // buang semua karakter selain angka
                        $number = preg_replace('/\D/', '', $state);

                        // normalize ke format 62xxxxxxxxxx
                        if (str_starts_with($number, '0')) {
                            $number = '62' . substr($number, 1);
                        } elseif (str_starts_with($number, '8')) {
                            $number = '62' . $number;
                        } elseif (!str_starts_with($number, '62')) {
                            $number = '62' . $number;
                        }

                        return $number;
                    })
                    ->afterStateHydrated(function ($component, $state) {
                        // pas edit, tampilkan tanpa prefix 62 biar konsisten sama input +62
                        if (filled($state) && str_starts_with($state, '62')) {
                            $component->state(substr($state, 2));
                        }
                    }),

                TextInput::make('lokasi_tenant')
                    ->label('Nomor/Blok Tenant')
                    ->columnSpanFull()
                    ->placeholder('contoh: A-12 atau Blok B No. 5')
                    ->helperText('Isi nomor atau blok lokasi tenant')
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
