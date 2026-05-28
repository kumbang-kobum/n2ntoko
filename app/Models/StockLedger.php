<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'product_id', 'qty_base', 'movement_type',
        'reference_type', 'reference_id',
        'stock_before', 'stock_after', 'cost_per_unit', 'notes',
    ];

    protected $casts = [
        'qty_base'      => 'decimal:4',
        'stock_before'  => 'decimal:4',
        'stock_after'   => 'decimal:4',
        'cost_per_unit' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
