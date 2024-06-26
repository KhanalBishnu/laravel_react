<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\Module;

class RoleAndPermissionController extends Controller
{
    public function index(){
        $roles=Role::all();
        return $this->jsonResponse($roles, null,true,200);
    }

    public function store(Request $request){
        $data=$request->all();
        $validator=Validator::make($data,[
            'name'=>'required|unique:roles,name'
        ]);
        if($validator->fails()){
            return $this->jsonResponse(null,$validator->errors(),false,422);
        }
        try {
            $role=null;
            DB::transaction(function() use($data,&$role){
               $role= Role::create([
                    'name'=>$data['name']
                ]);

                if (!empty($data['permissionIds'])) {
                    $permissionIds = array_map('intval', $data['permissionIds']);
                    $role->syncPermissions($permissionIds);
                }
            });
            return $this->jsonResponse($role,'Role Created Successfully',true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);
        }
    }

    public function delete(Role $role){
        try {
            $role->syncPermissions([]);
            $role->delete();
            return $this->jsonResponse(null,'Role Deleted Successfully',true,200);
            
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);

        }
    }

    public function getRolePermissions(Role $role){
        try {
           $rolesNames=$role->permissions->pluck('name');
            return $this->jsonResponse($rolesNames,null,true,200);

        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);
        }
    }

    public function getPermissionList(Role $role){
        try {
            $data['modules'] = Module::with(['permissions' => function ($query) {
                $query->select('id', 'name', 'module_id');
            }])->get();
            $data['permissionIds']=getRolePermission($role);
            
            // foreach ($modules as $key => $module) {
            //     $modulePermission=$module->permissions->pluck('name')->toArray();
            //     $modulePerArr=[];
            //     foreach ($modulePermission as $key => $modulePer) {
            //         $modulePerArr[]=explode('|',$modulePer)[0];
            //     }
            //     $module['permissionName']=$modulePerArr;
            //     // $module->unsetRelation('permissions');

            // }
            return $this->jsonResponse($data,null,true,200);

        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);
        }
    }

    public function update(Request $request){
        $data=$request->all();
        // \Log::info($data['permissionids']);
          try {
            DB::transaction(function() use($data) {
                $role = Role::findOrFail($data['id']);
                
                $role->update(['name' => $data['name']]);
                if (!empty($data['permissionids'])) {
                    $permissionIds = array_map('intval', $data['permissionids']);
                    $role->syncPermissions($permissionIds);
                }else{
                    $role->syncPermissions([]);

                }
            });
            
            return $this->jsonResponse($data,'Role Updated Successfully',true,200);

        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);

        }
    }

    public function allPermissionList(){
        try {
            $modules= Module::with(['permissions' => function ($query) {
                $query->select('id', 'name', 'module_id');
            }])->get();
            return $this->jsonResponse($modules,null,true,200);

        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);
        }
    }

}
