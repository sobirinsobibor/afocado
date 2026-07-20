<?php

namespace Modules\Restaurant\Database\Factories;

use Modules\Restaurant\Models\RestaurantMenuItem;
use Modules\Restaurant\Models\RestaurantOrder;
use Modules\Restaurant\Models\RestaurantOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Restaurant\Models\RestaurantOrderItem>
 */
class RestaurantOrderItemFactory extends Factory
{
    protected $model = RestaurantOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $this->faker->numberBetween(15000, 85000);
        $subtotal = $quantity * $price;

        return [
            'order_id' => RestaurantOrder::factory(),
            'menu_item_id' => RestaurantMenuItem::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
            'notes' => $this->faker->optional()->randomElement([
                'Pedas level 1',
                'Tanpa cabe',
                'Extra sambal',
                'Nasi terpisah',
                'Tanpa bawang',
            ]),
            'status' => $this->faker->randomElement(['pending', 'preparing', 'ready', 'served']),
        ];
    }
}