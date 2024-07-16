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
    public function index(Request $request){
        $data=$request->all();
        $page=$data['page']?? 1;
        $limit=$data['paginatedValue'];
        $offset=($page-1)*$limit;
        try {
            // $data['users']=User::with(['roles'=>function($q){
            //     $q->select('name');
            // }])->get();

            $user=User::with('roles','media');
            $data['total']=$user->count();
            $data['users']=$user->limit($limit)->offset($offset)->get();

            foreach ($data['users'] as $key => $user) {
              $user['role']=$user->roles->pluck('name');
              $user->unsetRelation('roles');
            }
            $data['roles']=Role::get(['id','name']);
            $data['limit']=$limit;
            
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
                return  $this->jsonResponse(null,$validation->errors()->first(),false,400);
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
                // Notification::route('mail',$user->email)->notify(new SendCredentialNotify($data));
            });
           return $this->jsonResponse(null,'User Created Successfully',true,200);
          
        } catch (\Throwable $th) {
           return  $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }

    public function update(Request $request){
        $data=$request->all();
        try {
            $validation = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.$data['id'],
                'file' => 'nullable|mimes:jpg,png'

            ]);
            if ($validation->fails()) {
                return  $this->jsonResponse(null,$validation->errors()->first(),false,400);
            }
            DB::transaction(function() use($request,$data){
                $user=User::findOrFail($data['id']);
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
                if(array_key_exists('file',$data)){
                    if($user->hasMedia('user-img')){
                        $user->clearMediaCollection('user-img');
                    }
                    $user->addMedia($data['file'])->toMediaCollection('user-img');
                }
                if(!empty($data['roleId'])){
                    $role=array_map('intval',[$data['roleId']]);
                    $user->syncRoles($role);
                }
            });
           return $this->jsonResponse(null,'User Updated Successfully',true,200);
          
        } catch (\Throwable $th) {
           return  $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }

    public function delete(User $user){
        try {
            $user->delete();
            return  $this->jsonResponse(null,'User Deleted Successfully',true,200);
        } catch (\Throwable $th) {
            return  $this->jsonResponse(null,$th->getMessage(),false,500);
        }
    }
}
