<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) => $event);
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $user = auth()->user();
        $activity->properties = $activity->properties->merge([
            'causer_name'  => $user?->name ?? 'Sistem',
            'subject_name' => $this->getActivitySubjectName(),
        ]);
    }

    public function getActivitySubjectName(): string
    {
        return $this->name
            ?? $this->invoice_number
            ?? $this->title
            ?? '';
    }
}
