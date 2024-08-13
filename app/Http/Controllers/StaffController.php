<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class StaffController extends Controller
{
   public function getAllStaff(){
        $staff = Staffs::all();
        return response()->json($staff);
   } 

   //add staff//

   public function addstaff(Request $request){
    try{
    $username = Staffs::where('username', $request->input('username'))->first();
    if(!is_null($username)){
        return response()->json(['error'=>'username was already exist']);
    }
    $email = Staffs::where('email', $request->input('email'))->first();
    if(!is_null($email)){
        return response()->json(['error'=>'Email was already exist']);
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
                'username' => 'required',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^(?=.*[a-z])/',
                    'regex:/^(?=.*[A-Z])/',
                    'regex:/^(?=.*\d)/',
                ]
                ], $messages);  
                
                


                $staff = new Staffs([
                    'fullname' =>$validateData['fullname'],
                    'email' =>$validateData['email'],
                    'contact_no' => $validateData['contact_no'],
                    'role' => $validateData['role'],
                    'dob' => $validateData['dob'],
                    'access'=>'pending',
                    'username' =>$validateData['username'],
                    'password' =>Hash::make($validateData['password']),
                ]);
    
    $staff->save();
    return response()->json(['message'=>'Staff was Saved'],201);


}catch(\Exception $error){
    return response()->json(['error'=> $error->getMessage()]);
}


}

// staff by id

public function getStaffById($id){
    try{
    $staff = Staffs::find($id);
    if(is_null($staff)){
        return response()->json(['message'=>' Staff was not found'], 404);
    }
    return response()->json(['Staff'=>$staff],200);
}catch(\Exception $error){
    return response()->json(['error'=>$error->getMessage()],500);
}
}

   


   //Delete Staff

   public function deleteStaff($id){
    $staff = Staffs::find($id);
    if(is_null($staff)){
        return response()->json(['message'=>'Staff is not found'],404);
    }else{
        $staff->delete();
        return response()->json(['message'=>'Staff was deleted']);
    }
   }

   //update Staff

   public function updateStaff(Request $request , $id){
    try{
        $staff = Staffs::find($id);
       if(is_null($staff)){
        return response()->json(['message'=>'staff was not found'],404);
       }else{
        $staff->update([
            'fullname'=>$request->input('fullname'),
            'email'=>$request->input('email'),
            'contact_no'=>$request->input('contact_no'),
            // 'dob'=>$request->input('dob'),
            'role'=>$request->input('role')
        ]);
        return response()->json(['message'=>'Staff was updated'],201);
       } 
    }catch(\Exception $error ){
        return response()->json(['error'=>$error->getMessage()],500);
    }
   }

   public function getStaffByRole($role){
        return Staffs::where('role', $role)->get();
   }

   //staff profile

   public function profile(){
    $user = Auth::user();
    return response()->json($user);
   }
   
}

