<?php

namespace Modules\Pos\Filament\Resources\PosFoodcourtLocations\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Njxqlus\Filament\Components\Infolists\LightboxImageEntry;

class PosFoodcourtLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Group::make([
                            ImageEntry::make('photos')
                                ->label('photo')
                                ->disk('local')
                                ->square()
                                ->simpleLightbox('test')
                                
                        ])->columnSpan(1),
                       
                        Group::make([
                            TextEntry::make('name')->label('Nama Foodcourt'),
                            TextEntry::make('address')->label('Alamat'),
                            TextEntry::make('tenants_count')
                                ->label('Jumlah Tenant')
                                ->state(fn ($record) => $record->tenants()->count()),
                        ])->columnSpan(1),
                    ])
            ]);
    }
}
