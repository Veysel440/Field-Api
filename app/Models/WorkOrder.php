<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkOrder extends Model
{
    use LogsActivity;

    protected static function booted(): void
    {
        static::saving(function(WorkOrder $m){
            if (!is_null($m->lat) && !is_null($m->lng)) {
                $m->setAttribute('location', \DB::raw('ST_SRID(POINT('.$m->lng.','.$m->lat.'),4326)'));
            }
        });
    }

    protected static $logAttributes = ['title','status','customer_id'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected $fillable=['customer_id','asset_id','code','title','status','lat','lng'];
    public function items(){ return $this->hasMany(WorkOrderChecklistItem::class); }
}
