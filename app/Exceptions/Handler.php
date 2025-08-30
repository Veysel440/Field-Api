<?php

namespace App\Exceptions;

use Throwable;

class Handler
{
    public function render($request, Throwable $e)
    {
        $isApi = $request->is('api/*');
        if (!$isApi) return parent::render($request, $e);

        $code = 'server'; $status = 500; $details = null; $msg = 'Server error';

        if ($e instanceof \Illuminate\Auth\AuthenticationException) { $code='unauthorized'; $status=401; $msg='Unauthorized'; }
        elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) { $code='forbidden'; $status=403; $msg='Forbidden'; }
        elseif ($e instanceof \Illuminate\Validation\ValidationException) { $code='validation'; $status=422; $msg='Validation failed'; $details=$e->errors(); }
        elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) { $code='not_found'; $status=404; $msg='Not found'; }

        return response()->json(['code'=>$code,'message'=>$msg,'details'=>$details], $status);
    }

}
