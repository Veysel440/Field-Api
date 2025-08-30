<?php

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

it('workorder update activity log yazar', function () {
    $admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $wo = WorkOrder::factory()->create();

    $get = $this->getJson("/api/work-orders/{$wo->id}")->assertOk();
    $etag = $get->headers->get('ETag');

    $this->withHeader('If-Match', $etag)
        ->patchJson("/api/work-orders/{$wo->id}", ['title' => 'Updated'])
        ->assertOk();

    $exists = DB::table('activity_log')->where([
        'subject_type' => \App\Models\WorkOrder::class,
        'subject_id'   => $wo->id,
        'event'        => 'updated',
    ])->exists();

    expect($exists)->toBeTrue();
});
