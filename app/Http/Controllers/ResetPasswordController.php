<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Staffs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request){

       if(!$this->validateEmail($request->email)){
          return $this->failedResponse();
       }
       $this->send($request->email);
       return $this->successResponse();
    }


    public function send($email){
      $token = $this->createToken($email);
      Mail::to($email)->send(new ResetPasswordMail($token));
    }


    public function createToken($email){
      $oldToken = DB::table('password_reset_tokens')->where('email',$email)->first();
      if($oldToken){
         return $oldToken->token;
      }

      $token = Str::random(60);
        $this->saveToken($token, $email);
      return $token;
      
    }
    
    public function saveToken($token,$email){
      DB::table('password_reset_tokens')->insert([
         'email'=> $email,
         'token'=> $token,
         'created_at' => Carbon::now()

      ]);
    }


    public function validateEmail($email){
       return User::where('email', $email)->first() || Staffs::where('email', $email)->first();
    }


    public function failedResponse(){
        return response()->json(['error'=>'Email is not exist']);
    }


    public function successResponse(){
      return response()->json(['message'=>'Reset Email is send successfully! , please check your email']);
      
  }


  public function changePassword(Request $request){

   return $this->getResetPasswordFromTable($request)->count()> 0 ? $this->changeNewPassword($request) : 
   $this-> tokenIsNotFound()
   ;

  }

  private function getResetPasswordFromTable(Request $request){
     return DB::table('password_reset_tokens')->where(['email'=>$request->email, 'token'=>$request->resetToken]);
  }


  private function changeNewPassword(Request $request){

   $user = Staffs::where('email', $request ->email)->first();
   $user->update(['password' => Hash::make($request->password)]);
   $this->getResetPasswordFromTable($request)->delete();
   return response()->json(['message'=> 'Successfully Changed Password']);
     
  }

  private function tokenIsNotFound(){
      return response()->json(['error'=>'Email or Token is Incorrect']);
  }
}
