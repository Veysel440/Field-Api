<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Idempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->method(), ['POST','PUT','PATCH','DELETE'], true)) {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key');
        if (!$key) return $next($request);

        $fp = hash('sha256', json_encode([
            'm'=>$request->method(),
            'u'=>$request->path(),
            'q'=>$request->query(),
            'b'=>$request->all(),
        ], JSON_UNESCAPED_UNICODE));

        $row = DB::table('idempotency_keys')->where('key',$key)->first();
        if ($row) {
            if ($row->fingerprint !== $fp) {
                return response()->json(['code'=>'conflict','message'=>'idempotency_conflict'], 409);
            }
            if ($row->status && $row->body) {
                return response($row->body, (int)$row->status, ['Content-Type'=>'application/json']);
            }
        } else {
            try {
                DB::table('idempotency_keys')->insert([
                    'key'=>$key,'fingerprint'=>$fp,'status'=>null,'body'=>null,
                    'created_at'=>now(),'updated_at'=>now()
                ]);
            } catch (\Throwable $e) {

            }
        }

        /** @var Response $resp */
        $resp = $next($request);

        if ($resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300) {
            DB::table('idempotency_keys')->where('key',$key)->update([
                'status'=>$resp->getStatusCode(),
                'body'=>$resp->getContent(),
                'updated_at'=>now(),
            ]);
        }

        return $resp;
    }
}
