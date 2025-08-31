<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\ApiTry;

class CustomerController extends Controller
{
    use ApiTry;

    public function index(Request $r)
    {
        $this->authorize('viewAny', Customer::class);

        $q = Customer::query();
        if ($s = $r->query('q')) $q->where('name','like',"%$s%");
        $size = min(100, (int)$r->query('size', 20));
        $page = (int)$r->query('page', 1);
        $total = $q->count();
        $rows = $q->orderByDesc('id')->forPage($page,$size)->get();

        return response()->json(['data'=>$rows,'total'=>$total]);
    }

    public function store(StoreCustomerRequest $r)
    {
        $this->authorize('create', Customer::class);
        return $this->attempt(function() use ($r) {
            $m = Customer::create($r->validated());
            return $m;
        }, 201);
    }
}
