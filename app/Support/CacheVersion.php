<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheVersion {
    public static function get(string $prefix): int {
        return (int) (Cache::get("cv:$prefix") ?? 1);
    }
    public static function bump(string $prefix): void {
        $k="cv:$prefix";
        $v=(int)(Cache::get($k) ?? 1);
        Cache::forever($k, $v + 1);
    }
}
