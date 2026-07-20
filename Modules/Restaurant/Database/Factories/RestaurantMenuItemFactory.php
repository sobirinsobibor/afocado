<?php

namespace Modules\Restaurant\Database\Factories;

use Modules\Restaurant\Models\RestaurantCategory;
use Modules\Restaurant\Models\RestaurantMenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Restaurant\Models\RestaurantMenuItem>
 */
class RestaurantMenuItemFactory extends Factory
{
    protected $model = RestaurantMenuItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $indonesianFoods = [
            'Nasi Gudeg Yogya',
            'Rendang Padang',
            'Gado-gado Jakarta',
            'Soto Ayam Lamongan',
            'Bakso Malang',
            'Nasi Padang',
            'Ayam Bakar Taliwang',
            'Pecel Lele',
            'Nasi Liwet Solo',
            'Rawon Surabaya',
            'Pempek Palembang',
            'Gudeg Manggar',
            'Sate Ayam Madura',
            'Nasi Kuning Manado',
            'Ayam Geprek Bensu'
        ];

        $descriptions = [
            'Hidangan tradisional Indonesia yang kaya akan rempah-rempah',
            'Makanan khas daerah dengan cita rasa autentik',
            'Sajian spesial dengan bumbu rahasia turun temurun',
            'Kuliner nusantara yang menggugah selera',
            'Hidangan favorit dengan paduan rasa yang sempurna'
        ];

        return [
            'category_id' => RestaurantCategory::factory(),
            'name' => $this->faker->randomElement($indonesianFoods),
            'description' => $this->faker->randomElement($descriptions),
            'price' => $this->faker->numberBetween(15000, 85000),
            'image' => $this->faker->imageUrl(400, 300, 'food'),
            'is_available' => $this->faker->boolean(85),
        ];
    }
}