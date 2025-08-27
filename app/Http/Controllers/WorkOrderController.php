<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkOrderStoreRequest;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index(Request $r) {
        $q = WorkOrder::query();
        if ($s = $r->query('q')) $q->where(fn($w)=>$w->where('title','like',"%$s%")->orWhere('code','like',"%$s%"));
        $page = (int)$r->query('page',1);
        $data = $q->orderByDesc('id')->paginate(20, ['*'], 'page', $page);
        return ['data'=>$data->items(),'total'=>$data->total()];
    }
    public function store(WorkOrderStoreRequest $r) { return WorkOrder::create($r->validated()); }
    public function show(WorkOrder $workOrder) { return $workOrder; }

    public function mapPoints(Request $r) {
        $q = WorkOrder::query()->whereNotNull('lat')->whereNotNull('lng')->select('id','title','lat','lng');
        return $q->limit(5000)->get();
    }
}
