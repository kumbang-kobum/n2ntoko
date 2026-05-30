<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'category', 'description',
        'amount', 'expense_date', 'reference',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function categoryLabels(): array
    {
        return [
            'sewa'         => 'Sewa',
            'listrik'      => 'Listrik',
            'air'          => 'Air',
            'gaji'         => 'Gaji',
            'transport'    => 'Transport',
            'pemeliharaan' => 'Pemeliharaan',
            'lainnya'      => 'Lainnya',
        ];
    }
}
