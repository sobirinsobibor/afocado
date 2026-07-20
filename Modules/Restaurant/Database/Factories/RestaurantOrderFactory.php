<?php

namespace Modules\Restaurant\Database\Factories;

use Modules\Restaurant\Models\RestaurantOrder;
use Modules\Restaurant\Models\RestaurantTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Restaurant\Models\RestaurantOrder>
 */
class RestaurantOrderFactory extends Factory
{
    protected $model = RestaurantOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = $this->faker->numberBetween(50000, 500000);
        $tax = $totalAmount * 0.1;
        $grandTotal = $totalAmount + $tax;

        return [
            'restaurant_table_id' => RestaurantTable::factory(),
            'order_number' => 'ORD-' . $this->faker->unique()->numberBetween(100000, 999999),
            'customer_name' => $this->faker->name(),
            'total_amount' => $totalAmount,
            'tax' => $tax,
            'grand_total' => $grandTotal,
            'status' => $this->faker->randomElement(['pending', 'preparing', 'ready', 'served', 'completed']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid']),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'qris', 'e-wallet']),
            'notes' => $this->faker->optional()->sentence(),
            'completed_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }
}