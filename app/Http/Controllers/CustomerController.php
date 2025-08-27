<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $r) {
        $q = Customer::query();
        if ($s = $r->query('q')) $q->where('name','like',"%$s%");
        $page = (int)$r->query('page',1);
        $data = $q->orderByDesc('id')->paginate(20, ['*'], 'page', $page);
        return ['data'=>$data->items(),'total'=>$data->total()];
    }
    public function store(CustomerStoreRequest $r) {
        return Customer::create($r->validated());
    }
}
