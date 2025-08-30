<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\RefreshToken;

class PruneRefreshTokens extends Command {
    protected $signature='prune:refresh-tokens';
    protected $description='Purge expired or revoked refresh tokens';
    public function handle(){
        $n=RefreshToken::where('revoked',true)->orWhere('expires_at','<',now())->delete();
        $this->info("Deleted {$n} tokens");
    }
}
