<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable=['customer_id','asset_id','code','title','status','lat','lng'];
    public function items(){ return $this->hasMany(WorkOrderChecklistItem::class); }
}
