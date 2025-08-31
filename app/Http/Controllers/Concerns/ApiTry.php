<?php

namespace App\Http\Controllers\Concerns;


use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Throwable;


trait ApiTry
{
    /**
     * @param  callable(Request): mixed  $cb
     * @param  int  $statusOnSuccess
     * @param  array<string,string> $headers
     * @return JsonResponse|Response
     */
    protected function attempt(callable $cb, int $statusOnSuccess = 200, array $headers = []): JsonResponse|Response
    {
        /** @var Request $req */
        $req = request();

        try {
            $out = $cb($req);

            if ($out instanceof JsonResponse || $out instanceof Response || $out instanceof BaseResponse) {
                self::appendStdHeaders($out, $headers);
                return $out;
            }
            if ($out instanceof Responsable) {
                $resp = $out->toResponse($req);
                self::appendStdHeaders($resp, $headers);
                return $resp;
            }

            if (is_array($out) && array_is_list($out) && array_key_exists(0, $out)) {
                $payload = $out[0];
                $status  = is_int($out[1] ?? null) ? (int)$out[1] : $statusOnSuccess;
                $hdrs    = is_array($out[2] ?? null) ? (array)$out[2] : [];
                $resp = response()->json(
                    self::normalize($payload),
                    $status,
                    array_merge(self::stdHeaders($req), $headers, $hdrs)
                );
                self::maybeAttachEtag($resp);
                return $resp;
            }

            $payload = self::normalize($out);
            $resp = response()->json(
                $payload,
                $statusOnSuccess,
                array_merge(self::stdHeaders($req), $headers)
            );
            self::maybeAttachEtag($resp);
            return $resp;
        } catch (Throwable $e) {

            throw $e;
        }
    }

    /** @return array<string,string> */
    protected static function stdHeaders(Request $req): array
    {
        $rid = $req->header('X-Request-Id');
        return $rid ? ['X-Request-Id' => $rid] : [];
    }

    protected static function appendStdHeaders(BaseResponse $resp, array $extra): void
    {
        foreach ($extra as $k => $v) {
            $resp->headers->set($k, $v);
        }
        $rid = request()->header('X-Request-Id');
        if ($rid) {
            $resp->headers->set('X-Request-Id', $rid);
        }
        self::maybeAttachEtag($resp);
    }

    /** @param mixed $data @return mixed */
    protected static function normalize(mixed $data): mixed
    {
        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            return $data;
        }

        if ($data instanceof Paginator) {
            return [
                'data'  => $data->items(),
                'total' => method_exists($data, 'total') ? $data->total() : null,
            ];
        }

        if ($data instanceof Model) {
            return $data->toArray();
        }

        if ($data instanceof Collection) {
            return ['data' => $data->values(), 'total' => $data->count()];
        }

        return $data;
    }

    protected static function maybeAttachEtag(BaseResponse $resp): void
    {
        if ($resp->headers->has('ETag')) return;

        $ct = $resp->headers->get('Content-Type');
        if ($ct && !str_contains($ct, 'application/json')) return;

        $content = $resp->getContent();
        if ($content === false || $content === null) return;

        $resp->headers->set('ETag', 'W/"' . sha1($content) . '"');
    }
}
