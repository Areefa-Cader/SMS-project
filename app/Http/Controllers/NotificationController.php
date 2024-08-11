<?php

namespace App\Http\Controllers;

use App\Mail\MyTestEmail;
use App\Mail\StaffAppointments;
use App\Models\Appointments;
use App\Models\Reminders;
use App\Models\Staffs;
use App\Models\User;
use App\Notifications\NewAppointment;
use App\Notifications\UpcomingAppointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\Ignition\ErrorPage\Renderer;

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

   //  public function notifyNewAppointment($staffId){
   //     $reminder = Reminders::where('staff_id', $staffId)->get();
   //     return response()->json($reminder);
   //  }

   //sending notification for the staff to remind their upcoming appointments

   public function sendReminder(Request $request){
      $appointmentId = $request->input('appointmentId');

      $appointment = Appointments::with('staff')->find($appointmentId);

      try{

      if($appointment && $appointment->staff){
         $reminder = new reminders([
            'type'=> 'Reminder',
            'message'=> json_encode(['message'=>'You have an Appointment at'.$appointment->time]),
            'appointment_id'=>$appointmentId,
            'staff_id'=>$appointment->staff_id
         ]);

         $reminder->save();
         $appointment->staff->notify(new UpcomingAppointments($appointment));

         return response()->json(['message' => 'Notifaction sent successfully']);
      }
   }catch(\Exception $error){
      return response()->json(['error'=> $error->getMessage()]);
   }
   }

   //get unread notifications

   // public function getUnreadNotifications($staffId){
   //    $notification = Reminders::where('staff_id', $staffId)
   //                   ->where('is_read', false)
   //                   ->orderBy('created_at', 'desc')
   //                   ->get();

   //    return response()->json($notification);               
   // }

   // public function markAsRead($notificationId){
   //    $notification = Reminders::find($notificationId);

   //    if($notification){
   //       $notification->is_read = true;
   //       $notification->save();

   //       return response()->json(['message'=>'Notification marked as read']);
   //    }

   //    return response()->json(['message'=>'Notification not found']);
   // }

   public function getNotifications($staffId){
      $notifications = Reminders::where('staff_id' , $staffId)
                       ->orderBy('created_at' , 'desc')->get();
                       return response()->json(['notification' => $notifications]);

   }

   public function markAsRead($notificationId){
      $notification = Reminders::find($notificationId);
      if($notification->type === 'Reminder'){
         $notification->is_read = 1;
         $notification->save();
      }
      return response()->json(['success' => true]);

   }


   // public function getAdminNotification($id){
   //    $access = 

   // }
       

   


}
