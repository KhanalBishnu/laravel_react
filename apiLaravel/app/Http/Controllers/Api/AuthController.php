<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;

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
                // Passport::personalAccessTokensExpireIn(now()->addSecond(5));
                Passport::personalAccessTokensExpireIn(now()->addHour(8));
                $token = auth()->user()->createToken('token')->accessToken;
                $user=User::with('media')->findOrFail(auth()->id());
                return response()->json([
                    'response' => true,
                    'token' => $token,
                    'user' => $user,
                    'expirationInMinutes' => auth()->user()->createToken('token')->token->expires_at->diffInSeconds(now()),
                    'roles'=>$user->roles->pluck('name') ?? [],
                    // 'permissions' => $user->roles->first()->permissions->pluck('name') ?? [],
                    'user-permission'=>$user->getPermissionsViaRoles()->pluck('name') ??[]



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
/* 
public function test(){

    foreach ($contact_details as $contact_detail) {
        if ($contact_detail['update_id'] != null) {
            $person_profile = PersonProfile::find($contact_detail['update_id']);
            if ($person_profile) {
                $personData = $person_profile['person_profile'];
                if ($personData['first_name'] !== null && $personData['first_name'] !== '') {
                    $customer_kyc_designations_id[] = $person_profile['designation_id'];
                    foreach ($personData['person_profile_emails'] as $email) {
                        $email['customer_kyc_id'] = $jsonData['id'];
                        $email['person_profile_id'] = $person_profile['id'];
                        $this->personProfileEmailRepo->store($email);
    
                        if ($email['is_email']) {
                            CustomerCcEmail::create([
                                'customer_kyc_id' => $jsonData['id'],
                                'email' => $email['email_id'],
                            ]);
                        }
                    }
    
                    foreach ($personData['person_profile_telephones'] as $telephone) {
                        $telephone['customer_kyc_id'] = $jsonData['id'];
                        $telephone['person_profile_id'] = $person_profile['id'];
                        $this->personProfileTelephoneRepo->store($telephone);
    
                        if ($telephone['is_sms'] && strlen($telephone['phone_number']) == 10) {
                            DB::table('customer_sms_numbers')->insert([
                                'customer_kyc_id' => $jsonData['id'],
                                'phone_number' => $telephone['phone_number'],
                            ]);
                        }
                    }
                    $person_profile->update($contact_detail['person_profile']);
                } else {
                    // Handle cases where first_name is empty
                    $this->handleEmptyFirstName($person_profile, $jsonData);
                }
            }
        } else {
            // Handle cases where update_id is null
        }
    }
}
    
    
    private function handleEmptyFirstName($person_profile, $jsonData)
    {
        $contactDetail = CustomerContactDetail::where('customer_kyc_id', $jsonData['id'])
            ->where('person_profile_id', $person_profile->id)
            ->first();
    
        if ($contactDetail && $contactDetail['designation_id'] != 1) {
            $this->deletePersonProfile($person_profile, $jsonData);
        }
    }
    
    private function deletePersonProfile($person_profile, $jsonData)
    {
        $person_profile->personProfileTelephones()->delete();
        $person_profile->personProfileEmails()->delete();
        $person_profile->customerContactDetail()->delete();
    
        $user = User::where('person_profile_id', $person_profile->id)->first();
        if ($user) {
            $deginationUser = CustomerContactDetail::where('customer_kyc_id', $jsonData['id'])
                ->where('designation_id', 1)
                ->first();
            if ($deginationUser) {
                $user->person_profile_id = $deginationUser->person_profile_id;
                $user->save();
            }
        }
    
        $person_profile->delete();
    } */
    
}
