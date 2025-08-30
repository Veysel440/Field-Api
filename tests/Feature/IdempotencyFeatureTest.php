<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

test('idempotency same key + same body → cached response', function () {
    $admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $body = ['name' => 'ACME', 'phone' => '123'];
    $key = 'K-123';

    $first = $this->withHeader('Idempotency-Key', $key)
        ->postJson('/api/customers', $body)
        ->assertStatus(201)
        ->json();

    $second = $this->withHeader('Idempotency-Key', $key)
        ->postJson('/api/customers', $body)
        ->assertStatus(201)
        ->json();

    expect($second)->toEqual($first);
});

test('idempotency same key + different body → 409', function () {
    $admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $key = 'K-XYZ';

    $this->withHeader('Idempotency-Key', $key)
        ->postJson('/api/customers', ['name' => 'A'])
        ->assertStatus(201);

    $this->withHeader('Idempotency-Key', $key)
        ->postJson('/api/customers', ['name' => 'B'])
        ->assertStatus(409)
        ->assertJson(['code' => 'conflict']);
});
