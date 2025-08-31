<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function healthz()
    {
        return response()->json(['ok'=>true]);
    }

    public function ready()
    {
        try { DB::select('select 1'); } catch (\Throwable) {
            return response()->json(['ok'=>false,'dep'=>'db'], 503);
        }
        try { Storage::disk('public')->exists('.'); } catch (\Throwable) {
            return response()->json(['ok'=>false,'dep'=>'storage'], 503);
        }
        return response()->json(['ok'=>true]);
    }
}
