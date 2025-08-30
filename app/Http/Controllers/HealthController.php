<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller {
    public function healthz(){
        return response()->json(['ok'=>true,'ts'=>now()]);
    }
    public function ready(){
        try { DB::select('select 1'); } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'db'=>false],503);
        }
        try { Storage::disk('public')->exists('/'); } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'storage'=>false],503);
        }
        return response()->json(['ok'=>true]);
    }
}
