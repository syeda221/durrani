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
     * Financial & Profitability Accessors
     */
    public function getTotalPurchaseCostAttribute(): float
    {
        return (float) $this->purchases->sum('net_amount');
    }

    public function getTotalSalesRevenueAttribute(): float
    {
        return (float) $this->sales->sum('total_net');
    }

    public function getSoldGoodsCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->sold_qty * (float) $item->purchase_price;
        });
    }

    public function getRealizedProfitAttribute(): float
    {
        return (float) ($this->total_sales_revenue - $this->sold_goods_cost);
    }

    public function getProfitMarginPercentAttribute(): float
    {
        $revenue = $this->total_sales_revenue;
        if ($revenue <= 0) return 0;
        return round(($this->realized_profit / $revenue) * 100, 1);
    }

    public function getRemainingStockValueAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->remaining_qty * (float) $item->purchase_price;
        });
    }

    public function getUnloadedStockValueAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->unloaded_qty * (float) $item->purchase_price;
        });
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
