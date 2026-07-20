# Restaurant Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module into `Modules/Restaurant`.

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Restaurant\\": "Modules/Restaurant/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module’s migrations and views load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\Restaurant\RestaurantServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/Restaurant/Filament/Resources'), for: 'Modules\Restaurant\Filament\Resources')
->discoverPages(in: base_path('modules/Restaurant/Filament/Pages'), for: 'Modules\Restaurant\Filament\Pages')
```
Note: The module also registers resources via `RestaurantServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Restaurant/Database/Migrations
```

## Seeder
Seed sample Restaurant data:
```bash
php artisan db:seed --class="Modules\\Restaurant\\Database\\Seeders\\RestaurantCategorySeeder"
php artisan db:seed --class="Modules\\Restaurant\\Database\\Seeders\\RestaurantMenuItemSeeder"
php artisan db:seed --class="Modules\\Restaurant\\Database\\Seeders\\RestaurantTableSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)