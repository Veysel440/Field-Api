<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $role = optional($request->user())->getRoleNames()->first();
            $perMinute = match ($role) {
                'admin' => 300,
                'tech'  => 180,
                default => 120,
            };
            $key = optional($request->user())->id ?: $request->ip();

            return Limit::perMinute($perMinute)->by($key)->response(function (Request $r, array $headers) {
                $retry = $headers['Retry-After'] ?? 60;
                return Response::json(['code'=>'rate_limited','message'=>'Too many requests'], 429)
                    ->header('Retry-After', $retry);
            });
        });

        $this->routes(function () {
            require base_path('routes/api.php');
        });
    }
}
