<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
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

    public function userInfo(){
        return response()->json([
            'response'=>true,
            'user'=>auth()->user(),
        ]);
    }
}
