<?php

use App\Models\{User, WorkOrder, Attachment};
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\{Role,Permission};

beforeAll(function(){
    Role::firstOrCreate(['name'=>'admin']);
    Role::firstOrCreate(['name'=>'tech']);
});

it('admin ek yükler ve silebilir', function () {
    Storage::fake('public');

    $admin = User::factory()->create(); $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $wo = WorkOrder::factory()->create();

    $resp = $this->postJson('/api/attachments', [
        'file'=> \Illuminate\Http\UploadedFile::fake()->create('a.pdf', 10, 'application/pdf'),
        'entity'=> 'workOrder', 'id'=> $wo->id,
    ])->assertCreated();

    $id = $resp->json('id');
    $path = Attachment::find($id)->path;
    Storage::disk('public')->assertExists($path);

    $this->deleteJson("/api/attachments/{$id}")->assertNoContent();
    Storage::disk('public')->assertMissing($path);
    $this->deleteJson("/api/attachments/{$id}")->assertStatus(404); // idempotent değil, ikinci silme 404
});
