<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger; use Monolog\Processor\UidProcessor; use Monolog\Processor\WebProcessor;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    public function boot(): void
    {
        foreach (['stack','request'] as $ch) {
            Log::channel($ch)->getLogger()->pushProcessor(function(array $record){
                $record['extra']['request_id'] = request()->header('X-Request-Id');
                return $record;
            });
        }
    }
}
