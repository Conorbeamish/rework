<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WarehouseStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_uuid' => 'required|uuid|exists:products,uuid',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_uuid']);
        $quantity = $validated['quantity'];

        $availableStock = $product->immediateDespatch();
        
        if ($availableStock < $quantity) {
            return response()->json([
                'message' => 'Insufficient stock available',
                'available' => $availableStock,
                'requested' => $quantity,
            ], 422);
        }

        return DB::transaction(function () use ($product, $quantity) {
            $stocks = WarehouseStock::where('product_uuid', $product->uuid)
                ->where('quantity', '>', 0)
                ->orderByDesc('quantity')
                ->lockForUpdate()
                ->get();

            $order = Order::create([
                'order_status' => 'placed',
                'total' => $product->price * $quantity,
            ]);

            OrderItem::create([
                'order_uuid' => $order->uuid,
                'product_uuid' => $product->uuid,
                'price' => $product->price,
                'quantity' => $quantity,
                'total' => $product->price * $quantity,
            ]);

            $remainingQuantity = $quantity;
            
            foreach ($stocks as $stock) {
                if ($remainingQuantity <= 0) {
                    break;
                }

                $availableInWarehouse = $stock->quantity - $stock->threshold;
                
                if ($availableInWarehouse <= 0) {
                    continue;
                }

                $deductAmount = min($availableInWarehouse, $remainingQuantity);
                $stock->decrement('quantity', $deductAmount);
                $remainingQuantity -= $deductAmount;
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'data' => [
                    'order_uuid' => $order->uuid,
                    'total' => $order->total,
                ],
            ], 201);
        });
    }
}
