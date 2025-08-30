<?php


use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\{Role,Permission};

function acting(string $role) {
    $u = User::factory()->create();
    Role::firstOrCreate(['name'=>$role]);
    $u->assignRole($role);
    Sanctum::actingAs($u);
    return $u;
}

dataset('matrix', [
    ['viewer','POST','/api/work-orders',403],
    ['tech','POST','/api/work-orders',201],
    ['admin','POST','/api/work-orders',201],
]);

beforeAll(function(){
    Permission::firstOrCreate(['name'=>'workorder.create']);
    Permission::firstOrCreate(['name'=>'workorder.update']);
    $admin=Role::firstOrCreate(['name'=>'admin']);
    $tech =Role::firstOrCreate(['name'=>'tech']);
    $viewer=Role::firstOrCreate(['name'=>'viewer']);
    $admin->givePermissionTo(['workorder.create','workorder.update']);
    $tech->givePermissionTo(['workorder.create','workorder.update']);
});

it('role/permission matrix', function(string $role, string $m, string $uri, int $status){
    acting($role);
    $payload = ['code'=>Str::random(6), 'title'=>'X', 'status'=>'open', 'customer_id'=>\App\Models\Customer::factory()->create()->id];
    $res = $this->json($m, $uri, $payload);
    $res->assertStatus($status);
})->with('matrix');
