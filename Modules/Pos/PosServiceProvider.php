<?php

namespace Modules\Pos;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class PosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any bindings
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'pos');
        
        // Register Filament Resources
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\Pos\Filament\Resources\PosProductResource::class,
                    \Modules\Pos\Filament\Resources\PosProductCategoryResource::class,
                    \Modules\Pos\Filament\Resources\PosUnitResource::class,
                ]);
            });
        }
    }
}