<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasActivityLog;
    protected $fillable = [
        'employee_id', 'date', 'check_in', 'check_out',
        'status', 'notes', 'input_by', 'photo_in', 'photo_out',
    ];

    protected $casts = [
        'date'      => 'date',
        'check_in'  => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getActivitySubjectName(): string
    {
        return 'Absensi ' . ($this->employee?->name ?? "ID #{$this->employee_id}") . ' ' . ($this->date?->format('d/m/Y') ?? '');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'green',
            'izin'  => 'blue',
            'sakit' => 'yellow',
            'alpha' => 'red',
            default => 'gray',
        };
    }

    public function getDurasiAttribute(): ?string
    {
        if (!$this->check_in || !$this->check_out) return null;

        $in  = \Carbon\Carbon::parse($this->check_in);
        $out = \Carbon\Carbon::parse($this->check_out);
        $diff = $in->diff($out);

        return $diff->h . 'j ' . $diff->i . 'm';
    }
}
