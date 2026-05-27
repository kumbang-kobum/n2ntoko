<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = ['product_id', 'unit_name', 'conversion', 'is_base'];

    protected $casts = [
        'conversion' => 'decimal:4',
        'is_base'    => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class, 'unit_id');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'unit_id');
    }

    public function getEceranPriceAttribute(): ?float
    {
        return $this->prices->where('price_type', 'eceran')->first()?->price;
    }

    public function getGrosirPriceAttribute(): ?float
    {
        return $this->prices->where('price_type', 'grosir')->first()?->price;
    }
}
