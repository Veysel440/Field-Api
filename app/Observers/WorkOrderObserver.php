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
        CacheVersion::bump('work_orders');
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
