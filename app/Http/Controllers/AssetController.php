<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiTry;
use App\Http\Requests\AssetStoreRequest;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    use ApiTry;
    public function index(Request $r)
    {
        $this->authorize('viewAny', Asset::class);
        $q = Asset::query();
        if ($s = $r->query('q')) $q->where('name','like',"%$s%");
        $size = min(100, (int)$r->query('size', 20));
        $page = (int)$r->query('page', 1);
        $total = $q->count();
        $rows = $q->orderByDesc('id')->forPage($page,$size)->get();
        return response()->json(['data'=>$rows,'total'=>$total]);
    }

    public function store(Request $r)
    {
        $this->authorize('create', Asset::class);
        $v = $r->validate([
            'code'=>'required|string|max:64',
            'name'=>'required|string|max:255',
            'customer_id'=>'required|exists:customers,id',
            'lat'=>'nullable|numeric|between:-90,90',
            'lng'=>'nullable|numeric|between:-180,180',
        ]);
        return $this->attempt(fn()=> Asset::create($v), 201);
    }

    public function mapPoints(Request $r) {
        $q = Asset::query()->whereNotNull('lat')->whereNotNull('lng')->select('id','name','lat','lng');
        return $q->limit(5000)->get();
    }
}
