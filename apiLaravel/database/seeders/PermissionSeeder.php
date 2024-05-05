<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAndPermissionModule=Module::create(['title'=>'Role And Permission']);

        $permissions=[
            [
                'name'=>'View|'.$roleAndPermissionModule['title'],
                'guard_name'=>'api',
                'module_id'=>$roleAndPermissionModule['id']
            ],
            [
                'name'=>'View All|'.$roleAndPermissionModule['title'],
                'guard_name'=>'api',
                'module_id'=>$roleAndPermissionModule['id']
            ],
            [
                'name'=>'Create|'.$roleAndPermissionModule['title'],
                'guard_name'=>'api',
                'module_id'=>$roleAndPermissionModule['id']
            ],
            [
                'name'=>'Update|'.$roleAndPermissionModule['title'],
                'guard_name'=>'api',
                'module_id'=>$roleAndPermissionModule['id']
            ],
            [
                'name'=>'Delete|'.$roleAndPermissionModule['title'],
                'guard_name'=>'api',
                'module_id'=>$roleAndPermissionModule['id']
            ]
        ];
        $permissionIds=[];
        foreach ($permissions as $key => $permissionData) {
           $permission= Permission::create($permissionData);
           $permissionIds[]=$permission['id'];
        }
        $adminRole=Role::where('name','Admin')->first();
        $adminRole->syncPermissions($permissionIds);

    }
}
