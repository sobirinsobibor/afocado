<?php

namespace Modules\Restaurant\Database\Factories;

use Modules\Restaurant\Models\RestaurantCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Restaurant\Models\RestaurantCategory>
 */
class RestaurantCategoryFactory extends Factory
{
    protected $model = RestaurantCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Makanan Utama',
                'Minuman',
                'Dessert',
                'Appetizer',
                'Makanan Ringan'
            ]),
            'icon' => $this->faker->randomElement([
                '🍽️', '🥤', '🍰', '🥗', '🍿'
            ]),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}