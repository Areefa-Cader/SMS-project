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
                return response()->json(['message' => 'Username already exists']);
            }


            $email = Staffs::where('email', $request->input('email'))->first();

            if (!is_null($email)) {
                return response()->json(['message' => 'Email already exists']);
            }

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
                        ]);   
    
           
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

    // Find the user by username
    $user = User::where('username', $request->input('username'))->first();
    $staff =Staffs::where('username', $request->input('username'))->first();

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
                    return response()->json($response,200);
                    break;

                default:
                    return response()->json(["message" => "Invalid role"], 400);
            }
        // }else {
        //     return response()->json(["message" => "Incorrect Password"], 401);
        // }

            
        // }elseif($staff){
        //     if (Hash::check($request->password, $staff->password)) {
            
        //         $token = $staff->createToken('ACCESS_TOKEN')->accessToken;
     
        //         $response = [
        //             'token' => $token,
        //             'username' => $staff->username,
        
        //         ];
        //         return response()->json($response,200);
        //       return response()->json(["message"=>"Owner, you are successfully logged in"]);

        }else{
                return response()->json(["message" => "Incorrect Password"]); 
            }
        
    } else {
        return response()->json(["message" => "User does not exist"], 404);
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
