<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with('warehouseStocks.warehouse')->get();

        $data = $products->map(function ($product) {
            return [
                'uuid' => $product->uuid,
                'title' => $product->title,
                'description' => $product->description,
                'price' => $product->price,
                'allocated_to_orders' => $product->allocatedToOrders(),
                'physical_quantity' => $product->physicalQuantity(),
                'total_threshold' => $product->totalThreshold(),
                'immediate_despatch' => $product->immediateDespatch(),
                'warehouse_stock' => $product->warehouseStocks->map(function ($stock) {
                    return [
                        'warehouse_uuid' => $stock->warehouse_uuid,
                        'warehouse_name' => $stock->warehouse->name,
                        'quantity' => $stock->quantity,
                        'threshold' => $stock->threshold,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $data]);
    }
}
