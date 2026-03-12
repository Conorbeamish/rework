<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'order_status' => 'placed',
            'total' => 0,
        ];
    }

    public function dispatched(): static
    {
        return $this->state(['order_status' => 'dispatched']);
    }

    public function cancelled(): static
    {
        return $this->state(['order_status' => 'cancelled']);
    }
}
