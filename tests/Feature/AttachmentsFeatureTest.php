<?php

use App\Jobs\ScanAttachment;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

test('upload attachment dispatches scan job and stores file', function () {
    $admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    Storage::fake('public');
    Queue::fake();

    $wo = WorkOrder::factory()->create();

    $file = UploadedFile::fake()->create('a.pdf', 12, 'application/pdf');

    $res = $this->postJson('/api/attachments', [
        'file' => $file,
        'entity' => 'workOrder',
        'id' => $wo->id,
        'note' => 'doc',
    ])->assertStatus(201)
        ->assertJsonStructure(['id','name','size','url']);

    Queue::assertPushed(ScanAttachment::class, fn ($job) => $job->attachmentId === $res->json('id'));
});
