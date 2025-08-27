<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(LoginRequest $r) {
        if (!Auth::attempt($r->only('email','password'))) {
            return response()->json(['message'=>'Invalid credentials'], 401);
        }
        /** @var \App\Models\User $user */
        $user = $r->user();

        $access = $user->createToken('access', ['access']);
        $refreshPlain = Str::random(64);
        RefreshToken::create([
            'user_id'=>$user->id,
            'token'=> hash('sha256', $refreshPlain),
            'expires_at'=> now()->addDays(30),
        ]);

        return ['accessToken'=>$access->plainTextToken, 'refreshToken'=>$refreshPlain];
    }

    public function me(Request $r) {
        return $r->user()->only('id','name','email');
    }

    public function refresh(Request $r) {
        $r->validate(['refreshToken'=>['required','string']]);
        $hash = hash('sha256', $r->string('refreshToken'));
        $row = RefreshToken::where('token',$hash)->whereNull('revoked_at')->first();
        if (!$row || $row->expires_at->isPast()) {
            return response()->json(['message'=>'Invalid refresh token'], 401);
        }
        $user = $row->user ?? \App\Models\User::find($row->user_id);
        $row->update(['revoked_at'=>now()]);
        $newPlain = Str::random(64);
        RefreshToken::create([
            'user_id'=>$user->id,
            'token'=> hash('sha256', $newPlain),
            'expires_at'=> now()->addDays(30),
        ]);
        $access = $user->createToken('access', ['access']);
        return ['accessToken'=>$access->plainTextToken, 'refreshToken'=>$newPlain];
    }

    public function logout(Request $r) {
        $u = $r->user();
        $u?->currentAccessToken()?->delete();
        if ($rt = $r->string('refreshToken')) {
            RefreshToken::where('token', hash('sha256',$rt))->update(['revoked_at'=>now()]);
        }
        return ['ok'=>true];
    }
}
