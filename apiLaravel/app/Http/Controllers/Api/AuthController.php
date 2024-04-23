<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login','register']]);
    // }
    public function register(Request $request)
    {
        $data=$request->all();
        try {
            $validation = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'confirm_password'=>'required|same:password',
                'file' => 'required|mimes:jpg,png'

            ]);
            if ($validation->fails()) {
                return response()->json([
                    'response' => false,
                    'message' => $validation->errors()
                ]);
            }
           $response= DB::transaction(function() use($request,$data){
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);
                if(array_key_exists('file',$data)){
                    $user->addMedia($data['file'])->toMediaCollection('user-img');
                }
                $token = $user->createToken('token')->accessToken;
                return [
                    'token' => $token,
                    'user' => $user,
                ];
            });
           
            return response()->json([
                'token' => $response['token'],
                'user' => $response['user'],
                'response' => true,
                'message'=>'User Register Successfully! '
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'response' => false,
                    'message' => $validator->errors()
                ]);
            }
            $data = [
                'email' => $request->email,
                'password' => $request->password,
            ];
            if (auth()->attempt($data)) {
                $token = auth()->user()->createToken('token')->accessToken;
                $user=User::with('media')->findOrFail(auth()->id());
                return response()->json([
                    'response' => true,
                    'token' => $token,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'response' => false,
                    'message' => 'Credential Does Not Match!'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'message' => $th->getMessage()
            ]);        }
       
    }

    public function createUser(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validation->fails()) {
            return response()->json([
                'response' => false,
                'message' => $validation->errors()
            ]);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            'response' => true,
            'message' => 'User Created Successfully'
        ]);
    }

    public function userInfo()
    {
        return response()->json([
            'response' => true,
            'user' => auth()->user(),
        ]);
    }

    public function users()
    {
        return response()->json([
            'response' => true,
            'users' => User::get(['id', 'name', 'email'])
        ]);
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user) {
                $user->delete();
                return response()->json([
                    'response' => true,
                    'message' => 'User Deleted Successfully'
                ]);
            } else {
                return response()->json([
                    'response' => false,
                    'message' => 'Something Went Wrong!'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function editUser($id){
        try {
            $user=User::findOrFail($id);
            return response()->json(['user'=>$user,'response'=>true]);
        } catch (\Throwable $th) {
           return response()->json(['message'=>$th->getMessage(),'response'=>false]);
        }
    }
}
