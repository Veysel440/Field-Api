<?php

namespace App\Providers;

use App\Models\{Customer, Asset, WorkOrder, Attachment};
use App\Policies\{CustomerPolicy, AssetPolicy, WorkOrderPolicy, AttachmentPolicy};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Customer::class   => CustomerPolicy::class,
        Asset::class      => AssetPolicy::class,
        WorkOrder::class  => WorkOrderPolicy::class,
        Attachment::class => AttachmentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
