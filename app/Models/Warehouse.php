<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'slug',
        'geo_location',
        'address_1',
        'address_2',
        'town',
        'county',
        'postcode',
        'state_code',
        'country_code',
    ];

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_uuid', 'uuid');
    }
}
