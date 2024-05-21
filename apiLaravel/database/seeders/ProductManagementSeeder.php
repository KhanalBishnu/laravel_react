<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $module=Module::create(['title'=>'Product']);
        $permissions=[
            [
                'name'=>'View|'.$module['title'],
                'module_id'=>$module['id'],
                'guard_name'=>'api'
            ],
            [
                'name'=>'View All|'.$module['title'],
                'guard_name'=>'api',
                'module_id'=>$module['id']
            ],
            [
                'name'=>'Create|'.$module['title'],
                'guard_name'=>'api',
                'module_id'=>$module['id']
            ],
            [
                'name'=>'Update|'.$module['title'],
                'guard_name'=>'api',
                'module_id'=>$module['id']
            ],
            [
                'name'=>'Delete|'.$module['title'],
                'guard_name'=>'api',
                'module_id'=>$module['id']
            ]
        ];
        $permissionIds=[];
        foreach ($permissions as $key => $permissionData) {
           $permission= Permission::create($permissionData);
           $permissionIds[]=$permission['id'];
        }
        $adminRole=Role::where('name','Admin')->first();
        $adminRole->givePermissionTo($permissionIds);
        
    }
}
