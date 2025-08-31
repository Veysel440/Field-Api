<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistTemplateItem extends Model
{
    protected $fillable = ['checklist_template_id','title','sort'];
    public function template(){ return $this->belongsTo(ChecklistTemplate::class,'checklist_template_id'); }
}
