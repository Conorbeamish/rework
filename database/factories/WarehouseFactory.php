<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
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
            'name' => fake()->city() . ' Warehouse',
            'slug' => fake()->unique()->slug(),
            'geo_location' => fake()->latitude(-90, 90) . ',' . fake()->longitude(-180, 180),
            'address_1' => fake()->streetAddress(),
            'address_2' => fake()->optional()->secondaryAddress(),
            'town' => fake()->city(),
            'county' => fake()->state(),
            'postcode' => fake()->postcode(),
            'state_code' => null,
            'country_code' => 'GB',
        ];
    }
}
