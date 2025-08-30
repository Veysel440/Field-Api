<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;


class MapController
{
    public function assetsBbox(Request $r){
        [$w,$s,$e,$n] = array_map('floatval', explode(',', $r->query('bbox','-180,-85,180,85')));
        $q = Asset::query()->whereBetween('lng',[$w,$e])->whereBetween('lat',[$s,$n]);
        $total = $q->count();
        $rows = $q->limit(1000)->get(['id','lat','lng','name']);
        return response()->json(['data'=>$rows,'total'=>$total]);
    }

}
