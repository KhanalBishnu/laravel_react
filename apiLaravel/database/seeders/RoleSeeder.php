<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole=Role::create([
            'name'=>'Admin',
            'guard_name'=>'api'
        ]);
        $adminUser=User::create([
            'name'=>'Admin',
            'email'=>'admin@gmail.com',
            'password'=>bcrypt('asdfgh137'),
            'email_verified_at'=>now(),
        ]);
        $userRole=Role::create([
            'name'=>'User',
            'guard_name'=>'api'
        ]);
        $user=User::create([
            'name'=>'User',
            'email'=>'user@gmail.com',
            'password'=>bcrypt('asdfgh137'),
            'email_verified_at'=>now(),
        ]);
        
        $adminUser->assignRole($adminRole);
        $user->assignRole($userRole);

    }
}
