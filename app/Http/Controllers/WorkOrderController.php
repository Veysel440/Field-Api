<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiTry;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    use ApiTry;

    public function show($id)
    {
        $w = WorkOrder::findOrFail($id);
        $this->authorize('view', $w);
        $etag = '"'.sha1($w->updated_at->toRfc7231String()).'"';
        return response()->json($w)->setEtag($etag);
    }

    public function store(Request $r)
    {
        $this->authorize('create', WorkOrder::class);
        $v = $r->validate([
            'code'=>'required|string|max:64',
            'title'=>'required|string|max:255',
            'status'=>'required|in:open,in_progress,done',
            'customer_id'=>'required|exists:customers,id',
            'assigned_user_id'=>'nullable|exists:users,id',
            'lat'=>'nullable|numeric|between:-90,90',
            'lng'=>'nullable|numeric|between:-180,180',
        ]);
        return $this->attempt(fn()=> WorkOrder::create($v), 201);
    }

    public function update(Request $r, $id)
    {
        $w = WorkOrder::findOrFail($id);
        $this->authorize('update', $w);

        $current = '"'.sha1($w->updated_at->toRfc7231String()).'"';
        $ifMatch = $r->header('If-Match');
        if (!$ifMatch || trim($ifMatch) !== $current) {
            return response()->json(['code'=>'conflict','message'=>'ETag mismatch'], 412);
        }

        $v = $r->validate([
            'title'=>'sometimes|string|max:255',
            'status'=>'sometimes|in:open,in_progress,done',
            'lat'=>'sometimes|nullable|numeric|between:-90,90',
            'lng'=>'sometimes|nullable|numeric|between:-180,180',
            'assigned_user_id'=>'prohibited',
        ]);

        return $this->attempt(function() use ($w,$v) {
            $w->fill($v)->save();
            return $w->refresh();
        });
    }

    public function assign(Request $r, $id)
    {
        $w = WorkOrder::findOrFail($id);
        $this->authorize('assign', $w);

        $v = $r->validate(['assigned_user_id'=>'required|exists:users,id']);
        return $this->attempt(function() use ($w,$v) {
            $w->assigned_user_id = $v['assigned_user_id'];
            $w->save();
            return ['ok'=>true,'assigned_user_id'=>$w->assigned_user_id];
        });
    }

    public function mapPoints(Request $r) {
        $q = WorkOrder::query()->whereNotNull('lat')->whereNotNull('lng')->select('id','title','lat','lng');
        return $q->limit(5000)->get();
    }
}
