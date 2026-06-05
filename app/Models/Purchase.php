<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'supplier_id', 'user_id', 'invoice_number',
        'purchase_date', 'total_amount', 'paid_amount',
        'status', 'notes', 'buyer_name',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount'  => 'decimal:2',
        'paid_amount'   => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isOrdered(): bool   { return $this->status === 'ordered'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }

    public function getHutangAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'ordered'   => 'Dipesan',
            'confirmed' => 'Diterima',
            'paid'      => 'Lunas',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'yellow',
            'ordered'   => 'blue',
            'confirmed' => 'green',
            'paid'      => 'gray',
            default     => 'gray',
        };
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'PB-' . now()->format('Ymd') . '-';
        $last   = static::where('invoice_number', 'like', $prefix . '%')
                        ->latest('id')->value('invoice_number');
        $seq    = $last ? ((int) substr($last, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
