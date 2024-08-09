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
                // 'fullname.required' => 'Full name is required',
                // 'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'email.unique' => 'Email already exists',
                // 'contact_no.required' => 'Contact number is required',
                // 'role.required' => 'Role is required',
                // 'dob.required' => 'Date of birth is required',
                // 'status.in' => 'Status must be either active or inactive',
                // 'username.required' => 'Username is required',
                // 'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters long',
                'password.regex' => 'Password must include at least one lowercase letter, one uppercase letter, and one number',
            ];


            $validateData = $request->validate([
                        'fullname'=> 'required',
                        'email' => 'required|email|unique:users',
                        'contact_no' => 'required',
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
            'status'=>'inactive',
            'username' =>$validateData['username'],
            'password' =>Hash::make($validateData['password']),
        ]);

        $token = JWTAuth::fromUser($user);
     
                $response = [
                    'token' => $token,
                    'userRole'=>'staff',
                    'role'=> $user->role,
                    'status'=> $user->status,
                ];
               
                $user->save();
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
            return response()->json(["error" => "Incorrect Password"]);
        }

            
        }else{
            //staff login

            $staff =Staffs::where('username', $request->input('username'))->first();
            if($staff){

            if (Hash::check($credentials['password'], $staff->password)) {
                
            
                $token = JWTAuth::fromUser($staff);
     
                $response = [
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
                return response()->json(["error" => "Incorrect Password"]);
            }
        
    }
  else {
        return response()->json(["error" => "Incorrect Username"]);
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
            } elseif($role === 'staff') { // Corrected the typo here
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
