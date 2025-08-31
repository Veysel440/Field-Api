<?php

namespace App\Exceptions;

use Throwable;

class Handler
{
    public function register(): void
    {
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $req) {
            return response()->json(['code'=>'unauthorized','message'=>$e->getMessage() ?: 'unauthorized'], 401);
        });

        $this->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $req) {
            return response()->json(['code'=>'forbidden','message'=>'forbidden'], 403);
        });

        $this->renderable(function (\Illuminate\Validation\ValidationException $e, $req) {
            return response()->json([
                'code'=>'validation_error',
                'message'=>'validation_error',
                'details'=>$e->errors(),
            ], 422);
        });

        $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $req) {
            return response()->json(['code'=>'not_found','message'=>'not_found'], 404);
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $req) {
            $headers = $e->getHeaders() + ['Retry-After' => (string) max(1, (int) ($e->getHeaders()['Retry-After'] ?? 1))];
            return response()->json(['code'=>'rate_limited','message'=>'rate_limited'], 429, $headers);
        });

        $this->renderable(function (\Throwable $e, $req) {
            if (config('app.debug')) {
                return response()->json(['code'=>'server','message'=>$e->getMessage()], 500);
            }
            return response()->json(['code'=>'server','message'=>'server_error'], 500);
        });
    }

}
