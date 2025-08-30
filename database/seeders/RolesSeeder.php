<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $tech  = Role::firstOrCreate(['name' => 'tech']);
        $viewer= Role::firstOrCreate(['name' => 'viewer']);

        if (!User::where('email','admin@example.com')->exists()) {
            $u = User::create([
                'name'=>'Admin',
                'email'=>'admin@example.com',
                'password'=>Hash::make('secret123'),
            ]);
            $u->assignRole($admin);
        }
    }
}
