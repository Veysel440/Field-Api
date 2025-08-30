<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    protected $fillable = ['code','name','customer_id','lat','lng'];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (Asset $m) {
            if (!is_null($m->lat) && !is_null($m->lng)) {
                $m->setAttribute('location', DB::raw('ST_SRID(POINT('.$m->lng.','.$m->lat.'),4326)'));
            }
        });
    }
}
