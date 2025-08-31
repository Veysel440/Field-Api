<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiTry;
use App\Models\Attachment;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use ApiTry;

    public function index(Request $r)
    {
        $this->authorize('viewAny', Attachment::class);
        $type = $r->query('type'); $id = $r->query('id');

        $q = Attachment::query();
        if ($type && $id) $q->where(['attachable_type'=>$type,'attachable_id'=>(int)$id]);

        $size = min(100, (int)$r->query('size', 50));
        $page = (int)$r->query('page', 1);
        $total = $q->count();
        $rows = $q->orderByDesc('id')->forPage($page,$size)->get()
            ->map(fn($a)=>[
                'id'=>$a->id,
                'name'=>$a->name,
                'size'=>$a->size,
                'mime'=>$a->mime,
                'url'=>Storage::disk('public')->url($a->path),
            ]);

        return response()->json(['data'=>$rows, 'total'=>$total]);
    }

    public function listWO(WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);
        $rows = Attachment::where([
            'attachable_type'=>WorkOrder::class,
            'attachable_id'=>$workOrder->id,
        ])->orderByDesc('id')->get()
            ->map(fn($a)=>[
                'id'=>$a->id,'name'=>$a->name,'size'=>$a->size,'mime'=>$a->mime,
                'url'=>Storage::disk('public')->url($a->path),
            ]);

        return response()->json(['data'=>$rows]);
    }

    public function store(Request $r)
    {
        $this->authorize('create', Attachment::class);

        $v = $r->validate([
            'file'=>'required|file|max:5120|mimetypes:image/png,image/jpeg,application/pdf,application/zip',
            'entity'=>'required|in:workOrder,asset,customer',
            'id'=>'required|integer',
            'note'=>'nullable|string',
        ]);

        return $this->attempt(function() use ($r, $v) {
            $f = $r->file('file');
            $path = $f->store('attachments', 'public');
            $type = match($v['entity']){
                'workOrder'=> \App\Models\WorkOrder::class,
                'asset'    => \App\Models\Asset::class,
                'customer' => \App\Models\Customer::class,
            };
            $att = Attachment::create([
                'attachable_type'=>$type,
                'attachable_id'=>$v['id'],
                'name'=>$f->getClientOriginalName(),
                'path'=>$path,
                'size'=>$f->getSize(),
                'mime'=>$f->getMimeType() ?: 'application/octet-stream',
                'meta'=>['note'=>$v['note'] ?? null],
            ]);
            dispatch(new \App\Jobs\ScanAttachment($att->id));
            return [
                'id'=>$att->id,'name'=>$att->name,'size'=>$att->size,'mime'=>$att->mime,
                'url'=>Storage::disk('public')->url($att->path)
            ];
        }, 201);
    }
}
