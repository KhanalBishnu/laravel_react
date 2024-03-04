<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['login']]);
    }
    public function register(Request $request){
        $validation= Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validation->fails()){
            return response()->json([
                'response'=>false,
                'message'=>$validation->errors()
            ]);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        $token=$user->createToken('token')->accessToken;
        return response()->json([
            'token'=>$token,
            'user'=>$user,
            'response'=>true
        ]);
    }


    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|string',
            'password'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'response'=>false,
                'message'=>$validator->errors()
            ]);
        }
        $data=[
            'email'=>$request->email,
            'password'=>$request->password,
        ];
        if(auth()->attempt($data)){
            $token = auth()->user()->createToken('token')->accessToken;
            return response()->json([
                'response'=>true,
                'token'=>$token,
                'user'=>auth()->user()
            ]);
        }else{
            return response()->json([
                'response'=>false,
                'message'=>'Credential Does Not Match!'
            ]);
        }
    }

    public function createUser(Request $request){
        $validation= Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validation->fails()){
            return response()->json([
                'response'=>false,
                'message'=>$validation->errors()
            ]);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        return response()->json([
            'response'=>true,
            'message'=>'User Created Successfully'
        ]);
    }

    public function userInfo(){
        return response()->json([
            'response'=>true,
            'user'=>auth()->user(),
        ]);
    }

    public function users(){
        return response()->json([
            'response'=>true,
            'users'=>User::get(['id','name','email'])
        ]);
    }
}
