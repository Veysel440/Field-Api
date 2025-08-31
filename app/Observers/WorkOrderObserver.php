<?php

namespace App\Observers;

use App\Models\WorkOrder;
use App\Support\CacheVersion;

class WorkOrderObserver
{
    public function created(WorkOrder $wo): void
    {
        CacheVersion::bump('work_orders');
    }

    public function updated(WorkOrder $wo): void
    {
        \App\Support\CacheVersion::bump('work_orders');

        try {
            $dirty = $wo->getChanges();
            unset($dirty['updated_at']);
            if ($dirty) {
                \DB::table('activity_log')->insert([
                    'subject_type'=> WorkOrder::class,
                    'subject_id'  => $wo->id,
                    'event'       => 'updated',
                    'causer_id'   => auth()->id(),
                    'changes'     => json_encode($dirty, JSON_UNESCAPED_UNICODE),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        } catch (\Throwable) {
        }
    }

    public function deleted(WorkOrder $wo): void
    {
        CacheVersion::bump('work_orders');
    }

    public function restored(WorkOrder $wo): void
    {
        CacheVersion::bump('work_orders');
    }

    public function forceDeleted(WorkOrder $wo): void
    {
        CacheVersion::bump('work_orders');
    }
}
