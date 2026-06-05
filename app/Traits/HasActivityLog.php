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
        if ($user) {
            $activity->properties = $activity->properties->merge([
                'causer_name' => $user->name,
            ]);
        }
    }
}
