<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RoleSeeder::class);
        \App\Models\Product::factory(50)->create();
        $this->call(PermissionSeeder::class);
        $this->call(UserManagementSeeder::class);
        $this->call(ProductManagementSeeder::class);

    }
}
