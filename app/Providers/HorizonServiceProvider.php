<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Horizon::auth(function ($request) {
            if (app()->isLocal()) return true;
            $user = $request->user();
            return $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
        });
    }
}
