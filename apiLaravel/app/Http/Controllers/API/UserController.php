<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendCredentialNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Validator;

class UserController extends Controller
{
    public function index(){
        try {
            // $data['users']=User::with(['roles'=>function($q){
            //     $q->select('name');
            // }])->get();

            $data['users']=User::with('roles')->get();
            foreach ($data['users'] as $key => $user) {
              $user['role']=$user->roles->pluck('name');
              $user->unsetRelation('roles');
            }
            $data['roles']=Role::get(['id','name']);
            
           return $this->jsonResponse($data,null,true,200);
        } catch (\Throwable $th) {
         return  $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }
    public function store(Request $request){
        $data=$request->all();
        try {
            $validation = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'file' => 'nullable|mimes:jpg,png'

            ]);
            if ($validation->fails()) {
                return response()->json([
                    'response' => false,
                    'message' => $validation->errors()
                ]);
            }
            DB::transaction(function() use($request,$data){
                $password='asdfgh137';
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($password),
                ]);
                if(array_key_exists('file',$data)){
                    $user->addMedia($data['file'])->toMediaCollection('user-img');
                }
                if(!empty($data['roleId'])){
                    $role=array_map('intval',[$data['roleId']]);
                    $user->assignRole($role);
                }
                $data['user']=$user;
                $data['password']=$password;
                Notification::route('mail',$user->email)->notify(new SendCredentialNotify($data));
            });
           return $this->jsonResponse(null,'User Created Successfully',true,200);
          
        } catch (\Throwable $th) {
           return  $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }
}
