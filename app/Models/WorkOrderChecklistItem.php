<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderChecklistItem extends Model
{
    protected $fillable = ['work_order_id','title','done','sort'];
    protected $casts = ['done'=>'bool'];
    public function workOrder(){ return $this->belongsTo(WorkOrder::class); }
}
