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
        return response()->json(['message'=>'username was already exist']);
    }
    $email = Staffs::where('email', $request->input('email'))->first();
    if(!is_null($email)){
        return response()->json(['message'=>'Email was already exist']);
    }
    $staff = new Staffs([
        'fullname'=>$request->input('fullname'),
        'email'=>$request->input('email'),
        'contact_no'=>$request->input('contact_no'),
        'dob'=>$request->input('dob'),
        'role'=>$request->input('role'),
        'status'=>'inactive',
        'username' =>$request->input('username'),
        'password' =>Hash::make($request->input('password')),

    ]);

    
    $staff->save();
    return response()->json(['message'=>'Staff was Saved'],201);


}catch(\Exception $error){
    return response()->json(['error'=> $error->getMessage()],500);
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
        return response()->json(['message'=>'customer was not found'],404);
       }else{
        $staff->update([
            'fullname'=>$request->input('fullname'),
            'email'=>$request->input('email'),
            'contact_no'=>$request->input('contact_no'),
            'dob'=>$request->input('dob'),
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

