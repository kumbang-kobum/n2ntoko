<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'base_unit_id',
        'stock_qty',
        'avg_cost',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'stock_qty' => 'decimal:4',
        'avg_cost'  => 'decimal:4',
        'min_stock' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'base_unit_id');
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class)->orderByDesc('conversion');
    }

    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_qty <= 0) return 'habis';
        if ($this->stock_qty <= $this->min_stock) return 'menipis';
        return 'aman';
    }

    public static function generateSku(): string
    {
        // lockForUpdate efektif jika dipanggil dalam DB::transaction (di ProductController::store/update).
        // Di luar transaksi, berperilaku sebagai SELECT biasa untuk preview SKU di form.
        $last = static::withTrashed()->lockForUpdate()->latest('id')->value('id') ?? 0;
        return 'PRD-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
