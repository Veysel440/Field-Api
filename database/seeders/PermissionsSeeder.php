<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role,Permission};

class PermissionsSeeder extends Seeder {
    public function run(): void {
        $pUpd = Permission::firstOrCreate(['name'=>'workorder.update']);
        $pCreate = Permission::firstOrCreate(['name'=>'workorder.create']);

        $admin = Role::firstOrCreate(['name'=>'admin']);
        $tech  = Role::firstOrCreate(['name'=>'tech']);
        $viewer= Role::firstOrCreate(['name'=>'viewer']);

        $admin->givePermissionTo([$pUpd,$pCreate]);
        $tech->givePermissionTo([$pUpd,$pCreate]);
    }
}
