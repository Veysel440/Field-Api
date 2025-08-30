<?php

namespace App\Providers;

class EventServiceProvider
{
    public function boot(): void
    {
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\Asset::observe(\App\Observers\AssetObserver::class);
        \App\Models\WorkOrder::observe(\App\Observers\WorkOrderObserver::class);
    }

}
