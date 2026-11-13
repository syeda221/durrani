<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckConsignmentItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'loaded_qty'     => 'decimal:3',
        'purchase_price' => 'decimal:2',
        'sold_qty'       => 'decimal:3',
        'unloaded_qty'   => 'decimal:3',
        'remaining_qty'  => 'decimal:3',
    ];

    public function consignment()
    {
        return $this->belongsTo(TruckConsignment::class, 'truck_consignment_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
