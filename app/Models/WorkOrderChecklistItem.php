<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderChecklistItem extends Model { protected $fillable=['work_order_id','title','done','ord']; protected $casts=['done'=>'bool']; }
