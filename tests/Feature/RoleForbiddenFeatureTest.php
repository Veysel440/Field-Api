<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

test('viewer POST /work-orders → 403', function () {
    $viewer = User::factory()->create();
    Role::firstOrCreate(['name' => 'viewer']);
    $viewer->assignRole('viewer');
    Sanctum::actingAs($viewer);

    $this->postJson('/api/work-orders', [
        'code' => 'W1', 'title' => 'Demo', 'status' => 'open', 'customer_id' => 1
    ])->assertStatus(403);
});
