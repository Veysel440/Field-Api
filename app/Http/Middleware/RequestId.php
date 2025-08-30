<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestId
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->headers->get('X-Request-Id') ?: Str::uuid()->toString();
        $request->headers->set('X-Request-Id', $id);
        /** @var \Symfony\Component\HttpFoundation\Response $res */
        $res = $next($request);
        $res->headers->set('X-Request-Id', $id);
        return $res;
    }
}
