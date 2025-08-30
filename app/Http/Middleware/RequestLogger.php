<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestLogger
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        /** @var \Symfony\Component\HttpFoundation\Response $res */
        $res = $next($request);

        $ms = (int) ((microtime(true) - $start) * 1000);
        $uid = optional($request->user())->id;
        $role = optional($request->user())->getRoleNames()->first();

        Log::channel('request')->info('api', [
            'rid' => $request->header('X-Request-Id'),
            'm'   => $request->getMethod(),
            'p'   => $request->path(),
            'st'  => $res->getStatusCode(),
            'ms'  => $ms,
            'ip'  => $request->ip(),
            'ua'  => substr($request->userAgent() ?? '', 0, 180),
            'uid' => $uid,
            'role'=> $role,
        ]);

        return $res;
    }
}
