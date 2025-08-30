<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneIdempotency extends Command {
    protected $signature='prune:idempotency {--days=7}';
    protected $description='Purge old idempotency keys';
    public function handle(){
        $d=(int)$this->option('days');
        $n=DB::table('idempotency_keys')->where('created_at','<',now()->subDays($d))->delete();
        $this->info("Deleted {$n} keys");
    }
}
