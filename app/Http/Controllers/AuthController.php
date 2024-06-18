<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use Illuminate\Http\Request;
use App\Models\User;
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
    public function login(){

    }

    //profile Api
    public function profile(){

    }

    //refresh token Api
    public function refreshToken(){

    }

    //logout Api
    public function logout(){

    }
        
    
}
