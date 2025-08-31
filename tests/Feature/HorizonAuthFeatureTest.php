<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('horizon dashboard sadece admin', function () {
    $user = User::factory()->create(); // role yok
    Sanctum::actingAs($user);
    $this->get('/horizon')->assertStatus(403);

    $admin = User::factory()->create(); $admin->assignRole('admin');
    Sanctum::actingAs($admin);
    $this->get('/horizon')->assertOk();
});
