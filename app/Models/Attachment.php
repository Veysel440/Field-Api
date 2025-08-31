<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['attachable_type','attachable_id','name','path','size','mime','meta'];
    protected $casts = ['meta'=>'array'];
    public function attachable(){ return $this->morphTo(); }
}
