<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckConsignment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'arrival_date'  => 'date',
        'total_freight' => 'decimal:2',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'truck_consignment_id');
    }

    public function items()
    {
        return $this->hasMany(TruckConsignmentItem::class, 'truck_consignment_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'truck_consignment_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function unloadedWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'unloaded_warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate next consignment reference number, e.g. TRK-202611-0001
     */
    public static function generateNextConsignmentNo(): string
    {
        $prefix = 'TRK-' . date('Ym') . '-';
        $latest = self::where('consignment_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('consignment_no');

        if ($latest) {
            $seq = (int) substr($latest, strlen($prefix)) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
