<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait RespondsWithPagination {
    protected function pageOut(Paginator|array $data, int $total=null) {
        if ($data instanceof Paginator) {
            return ['data'=>$data->items(),'total'=>$data->total()];
        }
        return ['data'=>$data, 'total'=>$total ?? count($data)];
    }
}
