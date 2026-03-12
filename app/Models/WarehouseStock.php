<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseStock extends Model
{
    protected $table = 'warehouse_stock';

    public $incrementing = false;

    protected $fillable = [
        'warehouse_uuid',
        'product_uuid',
        'quantity',
        'threshold',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'threshold' => 'integer',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_uuid', 'uuid');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_uuid', 'uuid');
    }
}
