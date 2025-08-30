<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ReqCache {
    public static function remember(string $prefix, int $ttl, callable $cb){
        $req = request(); $ver = CacheVersion::get($prefix);
        $key = $prefix.":v{$ver}:".md5($req->path().'|'.http_build_query($req->query(), '', '&'));
        return Cache::remember($key, $ttl, $cb);
    }
}
