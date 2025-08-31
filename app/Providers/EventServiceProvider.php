<?php

namespace App\Providers;

use App\Models\{Customer, Asset, WorkOrder};
use App\Observers\{CustomerObserver, AssetObserver, WorkOrderObserver};
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ...
    ];

    public function boot(): void
    {
        Customer::observe(CustomerObserver::class);
        Asset::observe(AssetObserver::class);
        WorkOrder::observe(WorkOrderObserver::class);
    }
}
