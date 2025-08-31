<?php

use App\Models\{User, Customer};
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\{Role,Permission};

beforeAll(function(){
    Role::firstOrCreate(['name'=>'admin']);
    Role::firstOrCreate(['name'=>'tech']);
    Permission::firstOrCreate(['name'=>'customer.create']);
    Permission::firstOrCreate(['name'=>'customer.update']);
    Permission::firstOrCreate(['name'=>'customer.delete']);
});

it('customer CRUD', function () {
    $tech = User::factory()->create(); $tech->assignRole('tech');
    $admin = User::factory()->create(); $admin->assignRole('admin');

    Sanctum::actingAs($tech);
    $c = $this->postJson('/api/customers', ['name'=>'ACME','phone'=>'1'])->assertCreated()->json();

    $this->getJson('/api/customers')->assertOk()->assertJsonStructure(['data','total']);
    $this->getJson('/api/customers/'.$c['id'])->assertOk()->assertJsonPath('name','ACME');

    $this->patchJson('/api/customers/'.$c['id'], ['phone'=>'2'])->assertOk()->assertJsonPath('phone','2');

    Sanctum::actingAs($admin);
    $this->deleteJson('/api/customers/'.$c['id'])->assertNoContent();
});
