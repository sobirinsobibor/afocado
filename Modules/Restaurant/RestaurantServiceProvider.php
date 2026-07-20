<?php

namespace Modules\Restaurant;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class RestaurantServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'restaurant');
        
        // Register Filament Resources
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\Restaurant\Filament\Resources\RestaurantTables\RestaurantTableResource::class,
                    \Modules\Restaurant\Filament\Resources\RestaurantOrders\RestaurantOrderResource::class,
                    \Modules\Restaurant\Filament\Resources\RestaurantMenuItems\RestaurantMenuItemResource::class,
                    \Modules\Restaurant\Filament\Resources\RestaurantCategories\RestaurantCategoryResource::class,
                ]);
            });
        }
    }
}