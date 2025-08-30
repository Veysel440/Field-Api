<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

function test(string $string, Closure $param)
{
    test('login + refresh', function () {
        $u = User::factory()->create([
            'email' => 'u@example.com',
            'password' => Hash::make('secret123'),
        ]);


        $res = $this->postJson('/api/auth/login', [
            'email' => 'u@example.com',
            'password' => 'secret123',
        ])->assertOk()
            ->assertJsonStructure(['accessToken','refreshToken','user' => ['id','email','role']])
            ->assertCookie('refresh_token');

        $refresh = $res->json('refreshToken');

        $this->withCookie('refresh_token', $refresh)
            ->postJson('/api/auth/refresh')
            ->assertOk()
            ->assertJsonStructure(['accessToken','refreshToken']);
    });
}
