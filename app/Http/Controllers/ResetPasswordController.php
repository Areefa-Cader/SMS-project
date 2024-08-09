<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Staffs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
      Mail::to($email)->send(new ResetPasswordMail);
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
}
