<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function issueTokens(User $user): array
    {
        $access = $user->createToken('access')->plainTextToken;

        $plain = bin2hex(random_bytes(32));
        RefreshToken::create([
            'user_id'    => $user->id,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addDays(14),
        ]);

        return ['accessToken'=>$access, 'refreshToken'=>$plain];
    }

    private function refreshCookie(string $plain): \Symfony\Component\HttpFoundation\Cookie
    {
        $secure = app()->isProduction();
        return cookie(
            'refresh_token',
            $plain,
            60 * 24 * 14,  // 14 gün
            '/', null, $secure, true, false, 'Strict'
        );
    }

    public function csrf(): JsonResponse
    {
        return response()->json(['ok'=>true])->withCookie(cookie('XSRF-TOKEN', csrf_token(), 120));
    }

    public function login(Request $r): JsonResponse
    {
        $v = $r->validate([
            'email' => ['required','email'],
            'password' => ['required','string','min:6'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $v['email'])->first();
        if (!$user || !Hash::check($v['password'], $user->password)) {
            throw ValidationException::withMessages(['email'=>'invalid_credentials']);
        }

        $pair = $this->issueTokens($user);

        return response()
            ->json(['accessToken'=>$pair['accessToken'], 'refreshToken'=>$pair['refreshToken'], 'user'=>[
                'id'=>$user->id,'email'=>$user->email,'role'=>$user->getRoleNames()->first()
            ]])
            ->withCookie($this->refreshCookie($pair['refreshToken']));
    }

    public function refresh(Request $r): JsonResponse
    {
        $plain = $r->cookie('refresh_token') ?: $r->input('refreshToken');
        if (!$plain) return response()->json(['code'=>'unauthorized','message'=>'no_refresh'], 401);

        $hash = hash('sha256', $plain);
        $row = RefreshToken::where('token_hash',$hash)->first();

        if (!$row || $row->revoked || ($row->expires_at && $row->expires_at->isPast())) {
            return response()->json(['code'=>'revoked','message'=>'refresh_revoked'], 401);
        }

        $user = User::findOrFail($row->user_id);

        $row->revoked = true; $row->save();

        $pair = $this->issueTokens($user);

        return response()
            ->json(['accessToken'=>$pair['accessToken'], 'refreshToken'=>$pair['refreshToken']])
            ->withCookie($this->refreshCookie($pair['refreshToken']));
    }

    public function me(Request $r): JsonResponse
    {
        /** @var User $u */
        $u = $r->user();
        return response()->json([
            'id'=>$u->id,'email'=>$u->email,'name'=>$u->name,
            'role'=>$u->getRoleNames()->first()
        ]);
    }

    public function logout(Request $r): JsonResponse
    {
        /** @var User $u */
        $u = $r->user();
        if ($t = $r->user()?->currentAccessToken()) $t->delete();

        if ($plain = $r->cookie('refresh_token')) {
            RefreshToken::where('token_hash', hash('sha256',$plain))->update(['revoked'=>true]);
        }

        $kill = cookie('refresh_token', '', -1, '/', null, app()->isProduction(), true, false, 'Strict');

        return response()->json(['ok'=>true])->withCookie($kill);
    }
}
