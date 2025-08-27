<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable=['customer_id','code','name','lat','lng'];
    public function customer(){ return $this->belongsTo(Customer::class); }
}
