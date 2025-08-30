<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\SecurityHeaders::class,
    ];

    protected $middlewareGroups = [
        'api' => [
            \App\Http\Middleware\RequestId::class,
            \App\Http\Middleware\RequestLogger::class,
            \App\Http\Middleware\ConditionalGet::class,
            \App\Http\Middleware\Idempotency::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        // Spatie
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];
}
