<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use Illuminate\Http\Request;
use App\Models\User;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
               
                $user->save();
                return response()->json(['message' => 'Saved Successfully!'], 201);
            
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()]);
        }

    }



    //login Api
    public function login(Request $request){

    // user login
    $user = User::where('username', $request->input('username'))->first();
    

    if($user) {
        
        if (Hash::check($request->password, $user->password)) {
            
            $token = $user->createToken('ACCESS_TOKEN')->accessToken;

            
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

            if (Hash::check($request->password, $staff->password)) {
            
                $token = $staff->createToken('ACCESS_TOKEN')->accessToken;
     
                $response = [
                    'token' => $token,
                    'userRole'=>'staff',
                    'username' => $staff->username,
        
                ];
                return response()->json(["response"=>$response,"message"=>$response['username']." ,successfully logged in"],200);

        }else{
                return response()->json(["error" => "Incorrect Password"]); 
            }
        
    }
  else {
        return response()->json(["error" => "Username and Password are incorrect"]);
    }

}
  
}   
    


    //profile Api
    public function profile(){

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
