<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Controllers\Concerns\RespondsWithPagination;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use RespondsWithPagination;

    public function index(Request $r){
        $q = Customer::query();
        if ($s = $r->query('q')) $q->where('name','like',"%$s%");
        $size = min(100, (int)$r->query('size', 20));
        $page = (int)$r->query('page', 1);
        $total = $q->count();
        $rows = $q->orderByDesc('id')->forPage($page,$size)->get();
        return response()->json([
            'data'=> CustomerResource::collection($rows),
            'total'=> $total
        ]);
    }

    public function store(StoreCustomerRequest $r){
        $c = Customer::create($r->validated());
        return (new CustomerResource($c))->response()->setStatusCode(201);
    }
}
