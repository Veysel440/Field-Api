<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChecklistTemplateStoreRequest;
use App\Models\ChecklistTemplate;
use App\Models\WorkOrder;
use App\Models\WorkOrderChecklistItem;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function templatesIndex() {
        return ['data'=> ChecklistTemplate::orderByDesc('id')->get()];
    }
    public function templatesStore(ChecklistTemplateStoreRequest $r) {
        return ChecklistTemplate::create($r->validated());
    }

    public function woList(WorkOrder $workOrder) {
        return ['data'=> $workOrder->items()->orderBy('ord')->get()];
    }
    public function woToggle(WorkOrder $workOrder, int $itemId, Request $r) {
        $r->validate(['done'=>['required','boolean']]);
        $item = WorkOrderChecklistItem::where('work_order_id',$workOrder->id)->where('id',$itemId)->firstOrFail();
        $item->update(['done'=>$r->boolean('done')]);
        return $item;
    }
    public function woAttachTemplate(WorkOrder $workOrder, Request $r) {
        $r->validate(['templateId'=>['required','exists:checklist_templates,id']]);
        $tpl = ChecklistTemplate::findOrFail($r->integer('templateId'));
        $ord = 0;
        foreach ($tpl->items as $title) {
            WorkOrderChecklistItem::create([
                'work_order_id'=>$workOrder->id,
                'title'=>$title,
                'ord'=>$ord++,
            ]);
        }
        return ['ok'=>true];
    }
}
