<?php

namespace Modules\Restaurant\Database\Seeders;

use Modules\Restaurant\Models\RestaurantCategory;
use Modules\Restaurant\Models\RestaurantMenuItem;
use Illuminate\Database\Seeder;

class RestaurantMenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainDishCategory = RestaurantCategory::where('name', 'Makanan Utama')->first();
        $drinkCategory = RestaurantCategory::where('name', 'Minuman')->first();
        $dessertCategory = RestaurantCategory::where('name', 'Dessert')->first();
        $appetizerCategory = RestaurantCategory::where('name', 'Appetizer')->first();
        $snackCategory = RestaurantCategory::where('name', 'Makanan Ringan')->first();

        // Makanan Utama
        $mainDishes = [
            ['name' => 'Nasi Gudeg Yogya', 'price' => 25000, 'description' => 'Gudeg khas Yogyakarta dengan ayam dan telur'],
            ['name' => 'Rendang Padang', 'price' => 35000, 'description' => 'Rendang daging sapi dengan bumbu rempah Padang'],
            ['name' => 'Gado-gado Jakarta', 'price' => 20000, 'description' => 'Salad sayuran dengan bumbu kacang khas Jakarta'],
            ['name' => 'Soto Ayam Lamongan', 'price' => 18000, 'description' => 'Soto ayam kuah bening dengan telur dan kerupuk'],
            ['name' => 'Bakso Malang', 'price' => 22000, 'description' => 'Bakso daging sapi dengan mie dan pangsit'],
            ['name' => 'Nasi Padang', 'price' => 30000, 'description' => 'Nasi dengan aneka lauk khas Padang'],
            ['name' => 'Ayam Bakar Taliwang', 'price' => 28000, 'description' => 'Ayam bakar pedas khas Lombok'],
            ['name' => 'Pecel Lele', 'price' => 15000, 'description' => 'Lele goreng dengan sambal dan lalapan'],
            ['name' => 'Rawon Surabaya', 'price' => 26000, 'description' => 'Sup daging dengan kuah hitam khas Surabaya'],
            ['name' => 'Pempek Palembang', 'price' => 24000, 'description' => 'Pempek ikan dengan kuah cuko'],
        ];

        if ($mainDishCategory) {
            foreach ($mainDishes as $dish) {
                RestaurantMenuItem::create([
                    'restaurant_category_id' => $mainDishCategory->id,
                    'name' => $dish['name'],
                    'description' => $dish['description'],
                    'price' => $dish['price'],
                    'is_available' => true,
                ]);
            }
        }

        // Minuman
        $drinks = [
            ['name' => 'Es Teh Manis', 'price' => 5000, 'description' => 'Teh manis dingin segar'],
            ['name' => 'Es Jeruk', 'price' => 8000, 'description' => 'Jus jeruk segar dengan es'],
            ['name' => 'Kopi Tubruk', 'price' => 7000, 'description' => 'Kopi hitam tradisional Indonesia'],
            ['name' => 'Es Kelapa Muda', 'price' => 12000, 'description' => 'Air kelapa muda segar'],
            ['name' => 'Jus Alpukat', 'price' => 15000, 'description' => 'Jus alpukat dengan susu kental manis'],
            ['name' => 'Es Cendol', 'price' => 10000, 'description' => 'Minuman tradisional dengan cendol dan santan'],
        ];

        if ($drinkCategory) {
            foreach ($drinks as $drink) {
                RestaurantMenuItem::create([
                    'restaurant_category_id' => $drinkCategory->id,
                    'name' => $drink['name'],
                    'description' => $drink['description'],
                    'price' => $drink['price'],
                    'is_available' => true,
                ]);
            }
        }

        // Dessert
        $desserts = [
            ['name' => 'Es Campur', 'price' => 12000, 'description' => 'Es serut dengan aneka topping'],
            ['name' => 'Klepon', 'price' => 8000, 'description' => 'Kue tradisional isi gula merah'],
            ['name' => 'Pisang Goreng', 'price' => 10000, 'description' => 'Pisang goreng crispy dengan madu'],
            ['name' => 'Kolak Pisang', 'price' => 9000, 'description' => 'Pisang dalam kuah santan manis'],
        ];

        if ($dessertCategory) {
            foreach ($desserts as $dessert) {
                RestaurantMenuItem::create([
                    'restaurant_category_id' => $dessertCategory->id,
                    'name' => $dessert['name'],
                    'description' => $dessert['description'],
                    'price' => $dessert['price'],
                    'is_available' => true,
                ]);
            }
        }

        // Appetizer
        $appetizers = [
            ['name' => 'Kerupuk Udang', 'price' => 5000, 'description' => 'Kerupuk udang renyah'],
            ['name' => 'Tahu Isi', 'price' => 8000, 'description' => 'Tahu goreng isi sayuran'],
            ['name' => 'Lumpia Semarang', 'price' => 12000, 'description' => 'Lumpia basah khas Semarang'],
            ['name' => 'Siomay Bandung', 'price' => 15000, 'description' => 'Siomay dengan bumbu kacang'],
        ];

        if ($appetizerCategory) {
            foreach ($appetizers as $appetizer) {
                RestaurantMenuItem::create([
                    'restaurant_category_id' => $appetizerCategory->id,
                    'name' => $appetizer['name'],
                    'description' => $appetizer['description'],
                    'price' => $appetizer['price'],
                    'is_available' => true,
                ]);
            }
        }

        // Makanan Ringan
        $snacks = [
            ['name' => 'Keripik Singkong', 'price' => 6000, 'description' => 'Keripik singkong renyah'],
            ['name' => 'Rempeyek Kacang', 'price' => 7000, 'description' => 'Rempeyek kacang tanah crispy'],
            ['name' => 'Kacang Rebus', 'price' => 5000, 'description' => 'Kacang tanah rebus dengan garam'],
        ];

        if ($snackCategory) {
            foreach ($snacks as $snack) {
                RestaurantMenuItem::create([
                    'restaurant_category_id' => $snackCategory->id,
                    'name' => $snack['name'],
                    'description' => $snack['description'],
                    'price' => $snack['price'],
                    'is_available' => true,
                ]);
            }
        }
    }
}