<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger; use Monolog\Processor\UidProcessor; use Monolog\Processor\WebProcessor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Monolog\LogRecord;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Monolog 2 ve 3 ile uyumlu processor
        app('log')->getLogger()->pushProcessor(function (LogRecord|array $record) {
            $rid = request()->header('X-Request-Id')
                ?? ($_SERVER['X_REQUEST_ID'] ?? Str::uuid()->toString());

            if ($record instanceof LogRecord) {
                // Monolog 3
                $record->extra['request_id'] = $rid;
                return $record;
            }

            // Monolog 2 (array)
            $record['extra']['request_id'] = $rid;
            return $record;
        });
    }
}
