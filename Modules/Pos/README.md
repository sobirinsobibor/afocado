# POS Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module into `Modules/Pos`.

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Pos\\": "Modules/Pos/"
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
    Modules\Pos\PosServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/Pos/Filament/Resources'), for: 'Modules\Pos\Filament\Resources')
->discoverPages(in: base_path('modules/Pos/Filament/Pages'), for: 'Modules\Pos\Filament\Pages')
```
Note: The module also registers resources via `PosServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Pos/Database/Migrations
```

## Seeder
Seed sample POS data:
```bash
php artisan db:seed --class="Modules\\Pos\\Database\\Seeders\\ProductSeeder"
php artisan db:seed --class="Modules\\Pos\\Database\\Seeders\\PosSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)