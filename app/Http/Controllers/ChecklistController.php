<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiTry;
use App\Models\ChecklistTemplate;
use App\Models\ChecklistTemplateItem;
use App\Models\WorkOrder;
use App\Models\WorkOrderChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    use ApiTry;

    public function templatesIndex(Request $r)
    {
        $this->authorize('viewAny', ChecklistTemplate::class);
        $q = ChecklistTemplate::query()->with('items')->orderBy('id','desc');
        $size = min(100, (int) $r->query('size', 50));
        $page = (int) $r->query('page', 1);
        $total = $q->count();
        $rows = $q->forPage($page, $size)->get();
        return response()->json(['data'=>$rows, 'total'=>$total]);
    }

    public function templatesStore(Request $r)
    {
        $this->authorize('create', ChecklistTemplate::class);
        $v = $r->validate([
            'title' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.sort' => 'nullable|integer|min:0',
        ]);
        return $this->attempt(function() use ($v){
            return DB::transaction(function () use ($v) {
                $tpl = ChecklistTemplate::create(['title'=>$v['title']]);
                $bulk = [];
                foreach ($v['items'] as $i) {
                    $bulk[] = new ChecklistTemplateItem([
                        'title'=>$i['title'],
                        'sort'=>$i['sort'] ?? 0,
                    ]);
                }
                $tpl->items()->saveMany($bulk);
                return $tpl->load('items');
            });
        }, 201);
    }

    public function woList(WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);
        $rows = WorkOrderChecklistItem::where('work_order_id',$workOrder->id)
            ->orderBy('sort')->orderBy('id')->get();
        return response()->json(['data'=>$rows]);
    }

    public function woToggle(WorkOrder $workOrder, int $itemId, Request $r)
    {
        $this->authorize('update', $workOrder);
        $v = $r->validate(['done'=>'required|boolean']);
        $row = WorkOrderChecklistItem::where('work_order_id',$workOrder->id)->findOrFail($itemId);
        $row->done = $v['done']; $row->save();
        return response()->json(['ok'=>true]);
    }

    public function woAttachTemplate(WorkOrder $workOrder, Request $r)
    {
        $this->authorize('update', $workOrder);
        $v = $r->validate(['template_id'=>'required|exists:checklist_templates,id']);
        return $this->attempt(function() use ($workOrder, $v) {
            return DB::transaction(function () use ($workOrder, $v) {
                $tpl = ChecklistTemplate::with('items')->findOrFail($v['template_id']);
                $present = WorkOrderChecklistItem::where('work_order_id',$workOrder->id)
                    ->pluck('title')->all();
                $bulk = [];
                foreach ($tpl->items as $i) {
                    if (in_array($i->title, $present, true)) continue;
                    $bulk[] = ['work_order_id'=>$workOrder->id,'title'=>$i->title,'done'=>false,'sort'=>$i->sort,'created_at'=>now(),'updated_at'=>now()];
                }
                if ($bulk) DB::table('work_order_checklist_items')->insert($bulk);
                return ['ok'=>true,'added'=>count($bulk)];
            });
        });
    }
}
