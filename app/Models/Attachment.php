<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model { protected $fillable=['entity_type','entity_id','name','path','size','mime']; }
