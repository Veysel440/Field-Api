<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function assetsBbox(Request $r)
    {
        [$w,$s,$e,$n] = array_map('floatval', explode(',', $r->query('bbox','-180,-85,180,85')));

        $envelope = "ST_SRID(ST_MakeEnvelope($w,$s,$e,$n),4326)";

        $base = Asset::query()
            ->whereNotNull('location')
            ->whereRaw("ST_Contains($envelope, location)");

        $total = (clone $base)->count();
        $rows  = (clone $base)->limit(1000)->get(['id','lat','lng','name']);

        return response()->json(['data'=>$rows, 'total'=>$total]);
    }

    public function workOrdersBbox(Request $r)
    {
        [$w,$s,$e,$n] = array_map('floatval', explode(',', $r->query('bbox','-180,-85,180,85')));
        $env = "ST_SRID(ST_MakeEnvelope($w,$s,$e,$n),4326)";
        $base = \App\Models\WorkOrder::query()
            ->whereNotNull('location')
            ->whereRaw("ST_Contains($env, location)");
        $total = (clone $base)->count();
        $rows = (clone $base)->limit(1000)->get(['id','lat','lng','code','status']);
        return response()->json(['data'=>$rows,'total'=>$total]);
    }
}
