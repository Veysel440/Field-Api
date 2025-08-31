<?php

use App\Models\{User, Asset, Customer};
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\{Role,Permission};

beforeAll(function(){
    Role::firstOrCreate(['name'=>'admin']);
    Role::firstOrCreate(['name'=>'tech']);
    Permission::firstOrCreate(['name'=>'asset.create']);
    Permission::firstOrCreate(['name'=>'asset.update']);
    Permission::firstOrCreate(['name'=>'asset.delete']);
});

it('tech create/update, admin delete', function () {
    $tech = User::factory()->create(); $tech->assignRole('tech');
    $admin = User::factory()->create(); $admin->assignRole('admin');
    $c = Customer::factory()->create();

    Sanctum::actingAs($tech);
    $a = $this->postJson('/api/assets', [
        'code'=>'A1','name'=>'Pump','customer_id'=>$c->id
    ])->assertCreated()->json();

    $this->getJson('/api/assets/'.$a['id'])->assertOk()->assertJsonPath('code','A1');

    $this->patchJson('/api/assets/'.$a['id'], ['name'=>'PumpX'])->assertOk()->assertJsonPath('name','PumpX');

    $this->deleteJson('/api/assets/'.$a['id'])->assertStatus(403);

    Sanctum::actingAs($admin);
    $this->deleteJson('/api/assets/'.$a['id'])->assertNoContent();
    $this->getJson('/api/assets/'.$a['id'])->assertStatus(404);
});
