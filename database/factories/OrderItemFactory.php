<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 9.99, 299.99);
        $quantity = fake()->numberBetween(1, 5);

        return [
            'order_uuid' => null,
            'product_uuid' => null,
            'price' => $price,
            'quantity' => $quantity,
            'total' => $price * $quantity,
        ];
    }
}
