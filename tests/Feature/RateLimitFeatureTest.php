<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

test('rate limit 429 with Retry-After', function () {
    RateLimiter::for('api', function () {
        return Limit::perMinute(1)->by('test-key');
    });

    $this->getJson('/api/customers')->assertStatus(200);

    $res = $this->getJson('/api/customers')->assertStatus(429);
    expect($res->headers->get('Retry-After'))->not->toBeNull();
});
