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
    public function update(Request $r, $id){
        $w=WorkOrder::findOrFail($id); $this->authorize('update',$w);
        $currentEtag = sha1($w->updated_at->toRfc7231String());
        $ifMatch = $r->header('If-Match');
        if(!$ifMatch || trim($ifMatch,'"') !== $currentEtag)
            return response()->json(['code'=>'conflict','message'=>'ETag mismatch'], 412);

        $v=$r->validate([
            'title'=>'sometimes|string',
            'status'=>'sometimes|in:open,in_progress,done',
            'customer_id'=>'sometimes|exists:customers,id'
        ]);
        $w->fill($v)->save();

        activity()->performedOn($w)->causedBy($r->user())->event('updated')->log('workorder.updated');

        $newEtag = sha1($w->updated_at->toRfc7231String());
        return response()->json($w)->setEtag($newEtag);
    }
    public function store(WorkOrderStoreRequest $r) { return WorkOrder::create($r->validated()); }
    public function show(WorkOrder $workOrder) { return $workOrder; }

    public function mapPoints(Request $r) {
        $q = WorkOrder::query()->whereNotNull('lat')->whereNotNull('lng')->select('id','title','lat','lng');
        return $q->limit(5000)->get();
    }
}
