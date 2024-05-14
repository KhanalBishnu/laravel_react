<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

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
            });
            return $this->jsonResponse($role,'Role Created Successfully',true,200);
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);
        }
    }

    public function delete(Role $role){
        try {
            $role->delete();
            return $this->jsonResponse(null,'Role Deleted Successfully',true,200);
            
        } catch (\Throwable $th) {
            return $this->jsonResponse(null,$th->getMessage(),false,403);

        }
    }
}
