<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'title',
        'description',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class, 'product_uuid', 'uuid');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_uuid', 'uuid');
    }

    public function allocatedToOrders(): int
    {
        return (int) $this->orderItems()
            ->whereHas('order', fn($query) => $query->where('order_status', 'placed'))
            ->sum('quantity');
    }

    public function physicalQuantity(): int
    {
        return $this->warehouseStocks()->sum('quantity') + $this->allocatedToOrders();
    }

    public function totalThreshold(): int
    {
        return (int) $this->warehouseStocks()->sum('threshold');
    }

    public function immediateDespatch(): int
    {
        return $this->warehouseStocks()->sum('quantity') - $this->totalThreshold();
    }
}
