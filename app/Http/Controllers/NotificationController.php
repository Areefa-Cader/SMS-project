<?php

namespace App\Http\Controllers;

use App\Mail\MyTestEmail;
use App\Mail\StaffAppointments;
use App\Models\Appointments;
use App\Models\Staffs;
use App\Models\User;
use App\Notifications\UpcomingAppointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    //Appointment Remainder 

    public function sendNotification($staffId, $appointmentId){

         $appointment = Appointments::find($appointmentId);

         if(!$appointment){
            return response()->json(['error'=>'Appointment not found']);
         }

         $appointmentDetails = [
            'id' => $appointment->id,
         ];

         // $user = User::find($id);
         $staff = Staffs::find($staffId);

         if($staff){
            $staff->notify(new UpcomingAppointments($appointmentDetails));
            return response()->json(['message'=>'Successfully sent']);
         }else{
            return response()->json(['error'=>'Staff not found']);
         }
    }

   


}
