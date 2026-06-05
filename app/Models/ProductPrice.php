<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasActivityLog;
    protected $fillable = ['product_id', 'unit_id', 'price_type', 'min_qty', 'price'];

    protected $casts = [
        'price'   => 'decimal:2',
        'min_qty' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
}
