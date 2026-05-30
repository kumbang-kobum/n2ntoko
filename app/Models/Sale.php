<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'user_id', 'invoice_number',
        'sale_date', 'price_type', 'payment_method',
        'subtotal_before_tax', 'tax_rate', 'tax_amount',
        'total_amount', 'paid_amount', 'change_amount',
        'status', 'notes',
    ];

    protected $casts = [
        'sale_date'          => 'date',
        'subtotal_before_tax'=> 'decimal:2',
        'tax_rate'           => 'decimal:2',
        'tax_amount'         => 'decimal:2',
        'total_amount'       => 'decimal:2',
        'paid_amount'        => 'decimal:2',
        'change_amount'      => 'decimal:2',
    ];

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'debit' => 'Debit/Transfer',
            'qris'  => 'QRIS',
            default => 'Tunai',
        };
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(SaleItem::class); }

    public function getTotalProfitAttribute(): float
    {
        return (float) $this->items->sum('profit');
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'JL-' . now()->format('Ymd') . '-';
        $last   = static::where('invoice_number', 'like', $prefix . '%')
                        ->latest('id')->value('invoice_number');
        $seq    = $last ? ((int) substr($last, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
