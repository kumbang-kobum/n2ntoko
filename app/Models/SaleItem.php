<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'unit_id',
        'qty', 'qty_base', 'sell_price', 'hpp_snapshot', 'subtotal', 'profit',
    ];

    protected $casts = [
        'qty'          => 'decimal:4',
        'qty_base'     => 'decimal:4',
        'sell_price'   => 'decimal:2',
        'hpp_snapshot' => 'decimal:4',
        'subtotal'     => 'decimal:2',
        'profit'       => 'decimal:2',
    ];

    public function sale()    { return $this->belongsTo(Sale::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function unit()    { return $this->belongsTo(ProductUnit::class, 'unit_id'); }
}
