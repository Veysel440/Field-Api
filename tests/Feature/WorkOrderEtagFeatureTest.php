<?php

use App\Models\User;
use App\Models\WorkOrder;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

test('PATCH with wrong If-Match → 412 conflict', function () {
    $admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $wo = WorkOrder::factory()->create();

    $get = $this->getJson("/api/work-orders/{$wo->id}")->assertOk();
    $etag = $get->headers->get('ETag');
    expect($etag)->not->toBeNull();

    $this->withHeader('If-Match', '"bogus-etag"')
        ->patchJson("/api/work-orders/{$wo->id}", ['title' => 'X'])
        ->assertStatus(412)
        ->assertJson(['code' => 'conflict']);
});
