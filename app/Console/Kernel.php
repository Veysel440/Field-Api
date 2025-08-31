<?php

namespace App\Console;

use Illuminate\Support\Facades\Schedule;

class Kernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('prune:idempotency --days=7')->dailyAt('02:10');
        $schedule->command('prune:refresh-tokens')->hourly();
        $schedule->command('cleanup:attachments')->dailyAt('03:00');
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }
    protected $commands = [
        \App\Console\Commands\PruneIdempotency::class,
        \App\Console\Commands\PruneRefreshTokens::class,
        \App\Console\Commands\CleanOrphanAttachments::class,
    ];

}
