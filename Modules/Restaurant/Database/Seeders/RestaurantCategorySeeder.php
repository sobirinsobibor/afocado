<?php

namespace Modules\Restaurant\Database\Seeders;

use Modules\Restaurant\Models\RestaurantCategory;
use Illuminate\Database\Seeder;

class RestaurantCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan Utama',
                'icon' => '🍽️',
                'is_active' => true,
            ],
            [
                'name' => 'Minuman',
                'icon' => '🥤',
                'is_active' => true,
            ],
            [
                'name' => 'Dessert',
                'icon' => '🍰',
                'is_active' => true,
            ],
            [
                'name' => 'Appetizer',
                'icon' => '🥗',
                'is_active' => true,
            ],
            [
                'name' => 'Makanan Ringan',
                'icon' => '🍿',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            RestaurantCategory::create($category);
        }
    }
}