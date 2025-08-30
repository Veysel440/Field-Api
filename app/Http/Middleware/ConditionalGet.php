<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConditionalGet
{
    public function handle(Request $req, Closure $next)
    {
        /** @var \Symfony\Component\HttpFoundation\Response $res */
        $res = $next($req);
        if ($req->method() !== 'GET') return $res;

        $etag = $res->headers->get('ETag');
        if (!$etag) {
            $last = $res->headers->get('Last-Modified');
            if ($last) $etag = '"'.sha1($last).'"';
            if ($etag) $res->headers->set('ETag', $etag);
        }
        $ifNone = $req->headers->get('If-None-Match');
        if ($etag && $ifNone && trim($ifNone) === $etag) {
            return response('', 304)->withHeaders(['ETag'=>$etag]);
        }
        return $res;
    }
}
