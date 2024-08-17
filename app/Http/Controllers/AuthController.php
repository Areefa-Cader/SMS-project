<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use Illuminate\Http\Request;
use App\Models\User;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //register Api
    public function register(Request $request){
        try {

            $username = Staffs::where('username', $request->input('username'))->first();
    
            if (!is_null($username)) {
                return response()->json(['error' => 'Username already exists']);
            }


            $email = Staffs::where('email', $request->input('email'))->first();

            if (!is_null($email)) {
                return response()->json(['error' => 'Email already exists']);
            }

            $messages = [
                'email.email' => 'Email must be a valid email address',
                'email.unique' => 'Email already exists',
                'contact_no.required' => 'The contact number must be 10 digits',
                'password.min' => 'Password must be at least 8 characters long',
                'password.regex' => 'Password must include at least one lowercase letter, one uppercase letter, and one number',
            ];


            $validateData = $request->validate([
                        'fullname'=> 'required',
                        'email' => 'required|email|unique:users',
                        'contact_no' => 'required | digits:10',
                        'role' => 'required',
                        'dob'=>'required',
                        'status'=>'in:active,inactive',
                        'username' => 'required',
                        'password' => [
                            'required',
                            'min:8',
                            'regex:/^(?=.*[a-z])/',
                            'regex:/^(?=.*[A-Z])/',
                            'regex:/^(?=.*\d)/',
                        ]
                        ], $messages);   
    
           
            $user = new Staffs([
            'fullname' =>$validateData['fullname'],
            'email' =>$validateData['email'],
            'contact_no' => $validateData['contact_no'],
            'role' => $validateData['role'],
            'dob' => $validateData['dob'],
            'access'=>'pending',
            'username' =>$validateData['username'],
            'password' =>Hash::make($validateData['password']),
        ]);

        $user->save();

        $token = JWTAuth::fromUser($user);
     
                $response = [
                    'token' => $token,
                    'userRole'=>'staff',
                    'role'=> $user->role,
                    'status'=> $user->status,
                ];
               
                
                return response()->json(['response'=>$response,'message' => 'Saved Successfully!'], 201);
            
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()]);
        }

    }



    //login Api
    public function login(Request $request){

        $credentials = $request->only('username', 'password');

        

    // user login
    $user = User::where('username', $request->input('username'))->first();
    

    if($user) {
        
        if (Hash::check($credentials['password'], $user->password)) {
            
            $token = JWTAuth::fromUser($user);

            $response = [
                'id'=>$user->id,
                'token' => $token,
                'userRole' => $user->role,
                'username' => $user->username,
            ];

            
            switch ($user->role ) {
                case 'admin':
                    $response['admin'] = $user;
                    return response()->json(["response"=>$response,"message"=>"Admin, successfully logged in!!"],200);
                    return response()->json();
                    break;

                case 'owner':
                    $response['owner'] = $user;
                    return response()->json(["response"=>$response,"message"=>"Owner, successfully logged in!!"],200);
                    break;

                default:
                    return response()->json(["error" => "Invalid role"],400);
            }
        }else {
            return response()->json(["error" => "Incorrect Username or Password"]);
        }

            
        }else{
            //staff login

            $staff =Staffs::where('username', $request->input('username'))->first();
            if($staff){

            if (Hash::check($credentials['password'], $staff->password)) {
                
            
                $token = JWTAuth::fromUser($staff);
     
                $response = [
                    'id'=>$staff->id,
                    'token' => $token,
                    'userRole'=>'staff',
                    'fullname'=> $staff->fullname,
                    'email'=> $staff->email,
                    'contact_no'=> $staff->contact_no,
                    'dob'=> $staff->dob,
                    'role'=> $staff->role,
                    'status'=> $staff->status,
                    'username' => $staff->username,
                    'password' => $staff->password,

        
                ];
                
                return response()->json(["response"=>$response,"message"=>$response['username']." ,successfully logged in"],200);

        }
            else {
                return response()->json(["error" => "Incorrect Username or Password"]);
            }
        
    }
  else {
        return response()->json(["error" => "Incorrect Username or Password"]);
    }

}
  
}   
    


    //profile Api
    public function profile(){

        $data = array();
        if(Session::has('loginId')) {
            $loginid = Session::get('loginId');
            $role = Session::get('role');
    
            if($role === 'admin' || $role === 'owner') {
                $data = User::where('id', $loginid)->first();
            } elseif($role === 'staff') { 
                $data = Staffs::where('id', $loginid)->first();
            }
        }
        return response()->json($data);
    }
    

    //refresh token Api
    public function refreshToken(){

    }

    //logout Api
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
        
    
}
