<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetStoreRequest;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $r) {
        $q = Asset::with('customer');
        if ($s = $r->query('q')) $q->where(fn($w)=>$w->where('name','like',"%$s%")->orWhere('code','like',"%$s%"));
        $page = (int)$r->query('page',1);
        $data = $q->orderByDesc('id')->paginate(20, ['*'], 'page', $page);
        return ['data'=>$data->items(),'total'=>$data->total()];
    }
    public function store(AssetStoreRequest $r) { return Asset::create($r->validated()); }

    // Map points
    public function mapPoints(Request $r) {
        $q = Asset::query()->whereNotNull('lat')->whereNotNull('lng')->select('id','name','lat','lng');
        return $q->limit(5000)->get();
    }
}
