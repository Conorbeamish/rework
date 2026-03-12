<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Database\Seeder;

class WarehouseStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                WarehouseStock::create([
                    'warehouse_uuid' => $warehouse->uuid,
                    'product_uuid' => $product->uuid,
                    'quantity' => fake()->numberBetween(10, 100),
                    'threshold' => fake()->numberBetween(0, 10),
                ]);
            }
        }
    }
}
