<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_allocated_to_orders_returns_zero_when_no_orders(): void
    {
        $product = Product::factory()->create();

        $this->assertEquals(0, $product->allocatedToOrders());
    }

    public function test_allocated_to_orders_sums_quantity_from_placed_orders(): void
    {
        $product = Product::factory()->create();

        $order1 = Order::factory()->create(['order_status' => 'placed']);
        OrderItem::create([
            'order_uuid' => $order1->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 5,
            'total' => $product->price * 5,
        ]);

        $order2 = Order::factory()->create(['order_status' => 'placed']);
        OrderItem::create([
            'order_uuid' => $order2->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 3,
            'total' => $product->price * 3,
        ]);

        $this->assertEquals(8, $product->allocatedToOrders());
    }

    public function test_allocated_to_orders_excludes_dispatched_orders(): void
    {
        $product = Product::factory()->create();

        $placedOrder = Order::factory()->create(['order_status' => 'placed']);
        OrderItem::create([
            'order_uuid' => $placedOrder->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 5,
            'total' => $product->price * 5,
        ]);

        $dispatchedOrder = Order::factory()->dispatched()->create();
        OrderItem::create([
            'order_uuid' => $dispatchedOrder->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 10,
            'total' => $product->price * 10,
        ]);

        $this->assertEquals(5, $product->allocatedToOrders());
    }

    public function test_allocated_to_orders_excludes_cancelled_orders(): void
    {
        $product = Product::factory()->create();

        $placedOrder = Order::factory()->create(['order_status' => 'placed']);
        OrderItem::create([
            'order_uuid' => $placedOrder->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 5,
            'total' => $product->price * 5,
        ]);

        $cancelledOrder = Order::factory()->cancelled()->create();
        OrderItem::create([
            'order_uuid' => $cancelledOrder->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 10,
            'total' => $product->price * 10,
        ]);

        $this->assertEquals(5, $product->allocatedToOrders());
    }

    public function test_total_threshold_sums_all_warehouse_thresholds(): void
    {
        $product = Product::factory()->create();
        $warehouse1 = Warehouse::factory()->create();
        $warehouse2 = Warehouse::factory()->create();

        WarehouseStock::create([
            'warehouse_uuid' => $warehouse1->uuid,
            'product_uuid' => $product->uuid,
            'quantity' => 50,
            'threshold' => 5,
        ]);

        WarehouseStock::create([
            'warehouse_uuid' => $warehouse2->uuid,
            'product_uuid' => $product->uuid,
            'quantity' => 30,
            'threshold' => 3,
        ]);

        $this->assertEquals(8, $product->totalThreshold());
    }

    public function test_total_threshold_returns_zero_when_no_stock(): void
    {
        $product = Product::factory()->create();

        $this->assertEquals(0, $product->totalThreshold());
    }

    public function test_immediate_despatch_calculates_quantity_minus_threshold(): void
    {
        $product = Product::factory()->create();
        $warehouse1 = Warehouse::factory()->create();
        $warehouse2 = Warehouse::factory()->create();

        WarehouseStock::create([
            'warehouse_uuid' => $warehouse1->uuid,
            'product_uuid' => $product->uuid,
            'quantity' => 50,
            'threshold' => 5,
        ]);

        WarehouseStock::create([
            'warehouse_uuid' => $warehouse2->uuid,
            'product_uuid' => $product->uuid,
            'quantity' => 30,
            'threshold' => 3,
        ]);

        // (50 + 30) - (5 + 3) = 80 - 8 = 72
        $this->assertEquals(72, $product->immediateDespatch());
    }

    public function test_immediate_despatch_returns_zero_when_no_stock(): void
    {
        $product = Product::factory()->create();

        $this->assertEquals(0, $product->immediateDespatch());
    }

    public function test_physical_quantity_sums_warehouse_stock_and_allocated(): void
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        WarehouseStock::create([
            'warehouse_uuid' => $warehouse->uuid,
            'product_uuid' => $product->uuid,
            'quantity' => 50,
            'threshold' => 5,
        ]);

        $order = Order::factory()->create(['order_status' => 'placed']);
        OrderItem::create([
            'order_uuid' => $order->uuid,
            'product_uuid' => $product->uuid,
            'price' => $product->price,
            'quantity' => 10,
            'total' => $product->price * 10,
        ]);

        // 50 (warehouse) + 10 (allocated) = 60
        $this->assertEquals(60, $product->physicalQuantity());
    }

    public function test_physical_quantity_returns_zero_when_no_stock_or_orders(): void
    {
        $product = Product::factory()->create();

        $this->assertEquals(0, $product->physicalQuantity());
    }
}
