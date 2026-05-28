<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'unit_id',
        'qty', 'qty_base', 'buy_price', 'buy_price_base', 'subtotal',
    ];

    protected $casts = [
        'qty'            => 'decimal:4',
        'qty_base'       => 'decimal:4',
        'buy_price'      => 'decimal:2',
        'buy_price_base' => 'decimal:4',
        'subtotal'       => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
}
