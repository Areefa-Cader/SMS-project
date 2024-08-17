<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Customers;
use App\Models\Invoices;
use App\Models\reminders;
use App\Models\Services;
use App\Models\Staffs;
use App\Notifications\NewAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Input\Input;

class AppointmentController extends Controller
{
   
    public function getAllAppointment(){
        try {
            $appointments = Appointments::with(['customer', 'staff', 'service'])->get();
    
            $formattedAppointments = $appointments->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'cusName' => $appointment->customer->fullname,
                    'empName' => $appointment->staff->fullname,
                    'service' => $appointment->service->service_name,
                    'date' => $appointment->date,
                    'time' => $appointment->time
                ];
            });
            return response()->json($formattedAppointments);
    
            return response()->json(['status' => true, 'appointments' => $formattedAppointments], 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }

    }

    public function addCustomerDetails(Request $request){
        try{
            $customer = Customers::where('contact_no', $request->input('contact_no'))->first();
            if(is_null($customer)){
                $customer = new Customers([
                    'fullname'=>$request->input('fullname'),
                    'email'=>$request->input('email'),
                    'contact_no'=>$request->input('contact_no'),
                    'gender'=>$request->input('gender'),
                    'address'=>$request->input('address'),
                ]);
                $customer->save();
                return response()->json(['customer'=>$customer]);
            }else{
                return response()->json(['customer'=>$customer]);
    
            }
        } catch(\Exception $error){
            return response()->json(['message'=>$error->getMessage()],500);
        }  
        }
    

    public function addAppointment(Request $request){
        // customer details
        try{

            $customer = Customers::find($request->input('customer_id'));
            if (is_null($customer)) {
                return response()->json(['error' => 'Customer not found'], 400);
            }

        //validate the staff

           $staff = Staffs::findOrFail($request->input('staff_id'));

           $serviceId = $request->input('service_id');


           if (empty($serviceId)) {
            return response()->json(['error' => 'Invalid service IDs'], 400);
        }
       

           $service = Services::find($serviceId);

            if (!$service) {
                return response()->json(['error' => "Service not found: $serviceId"], 400);
            }

                $validateAppointment = Appointments::where('staff_id', $staff->id)
                ->where('date', $request->input('date'))
                ->where('time', $request->input('time'))
                ->where('service_id', $service->id)
                ->first();

    
        if($validateAppointment){
            return response()->json(['error'=>'This time slot is already booked']);
        }
       


    
        //create a new appointment

        // $serviceIdsString = implode(',',$serviceIds);

        $appointment = new Appointments([
            'customer_id'=>$customer->id,
            'staff_id'=>$staff->id,
            'service_id'=>$service->id,
            'date'=>$request->input('date'),
            'time'=>$request->input('time')
        ]);

        if(!$appointment->save()){
            return response()->json(['error'=>'Error creating appointment']);
        }

        $invoice = new Invoices([
            'appointment_id'=>$appointment->id,
            'customer_name'=>$appointment->customer->fullname,
            'service_name'=>$service->service_name,
            'total_amount'=>$service->price,
            'issue_date'=>today(),
            'due_date'=>$appointment->date,
         ]);

        
        if(!$invoice->save()){
            $appointment->delete();
            return response()->json(['error'=>'Error creating Invoice ']);
        }

         $reminder = new reminders([
            'type'=> 'New Appointment',
            'message'=> json_encode('You have an Appointment at'.$appointment->date),
            'appointment_id'=>$appointment->id,
            'staff_id'=>$staff->id
         ]);

         $reminder->save();
         $staff= Staffs::find($appointment->staff_id);
         $staff->notify(new NewAppointment($appointment));


    
        // return response()->json(['message'=>'successfully added both Appointment and invoice'],200);
        return response()->json(['message'=>'successfull added both appointment and invoice',
     'appointment'=>$appointment,
     'invoice'=>$invoice,
    //  'reminder'=>$reminder
    ]);
    

    }catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
}

// private function convertDurationToMinutes($duration){
//     $timeParts = explode(':' , $duration);
//     return($timeParts[0] * 60) + $timeParts[1];
// }



//delete appointment

public function deleteAppointment($id){
    try{
    $appointment = Appointments::find($id);
    if(is_null($appointment)){
        return response()->json(['message'=>'Appointment was not found'],400);
    }else{
        $appointment->delete();
        return response()->json(['message'=>'successfully deleted'],200);
    }
}catch(\Exception $error){
    return response()->json(['error'=>$error->getMessage()],500);
}
}

//manage time slots
public function getAllTimeSlots(Request $request)
{

    $date = $request->input('date');
    $timeSlots = [];

    $appointments = Appointments::with(['staff', 'service'])->where('date', $date)->get();

    // return response()->json(['appointments'=>$appointments]);
    
    $allTimeSlots = [
         '09:00', '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00', '17:00', '18:00'
    ];

    foreach($allTimeSlots as $time){
        $bookedAppointment = $appointments->first(function($appointment) use ($date , $time){
            $appointmentTime = \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time)->format('H:i');
            return $appointment->date == $date && $appointmentTime == $time;
           

        });

        // return response()->json(['bookedappointment'=> $bookedAppointment]);

        if($bookedAppointment){
            $isSameStaffAndService = $appointments->contains(function($appointment) use ($date, $time, $bookedAppointment){
                $appointmentTime = \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time)->format('H:i');
                 return $appointment->date == $date && $appointmentTime == $time &&
                 $appointment->staff_id == $bookedAppointment->staff_id &&
                 $appointment->service_id == $bookedAppointment->service_id;

            });

            // return response()->json(['issamesaerviceandstaff'=> $isSameStaffAndService]);

            if($isSameStaffAndService){
                $timeSlots[]=[
                    'time' => $time,
                    'isBooked' => true,
                    'staffName' => $bookedAppointment->staff->fullname,
                    'serviceName' => $bookedAppointment->service->service_name
                ];
            }
                else{
                    $timeSlots[]=[
                        'time' => $time,
                        'isBooked' => false,
                        'staffName' =>'',
                        'serviceName' =>''
                    ];

                }
            
        }else{
            $timeSlots[]=[
                'time' => $time,
                'isBooked' => false,
                'staffName' =>'',
                'serviceName' =>''
            ];
        }
    }

    return response()->json(['timeSlots'=> $timeSlots]);
 
   
}
// Upcoming Appointment

public function getUpcomingAppointment() {
    try {
        $today = Carbon::now();
        $appointments = Appointments::with(['customer', 'staff', 'service'])
            ->where('date', '>', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $formattedAppointments = $appointments->map(function($appointment) {
            return [
                'id' => $appointment->id,
                'cusName' => $appointment->customer->fullname,
                'empName' => $appointment->staff->fullname,
                'service' => $appointment->service->service_name,
                'date' => $appointment->date,
                'time' => $appointment->time
            ];
            
        });
        return response()->json($formattedAppointments);
        return response()->json(['status' => true, 'appointments' => $formattedAppointments]);
    } catch (\Exception $error) {
        return response()->json(['error' => $error->getMessage()]);
    }
}

// staff availability

public function getStaffAvailability(Request $request) {
    $date = $request->input('date') ?? date('Y-m-d'); // Use the current date if not provided

    try {
        $staffs = Staffs::all();
        //it retrieves the staffs who have appointments, others status is empty means no appointments
        $appointments = Appointments::whereIn('staff_id', $staffs->pluck('id'))
                                    ->where('date', '>=', $date)
                                    ->get();
        // return response()->json(['appointments'=>$appointments]);                                        

        $availability = $staffs->map(function($staff) use ($appointments) {
            $staffAppointments = $appointments->where('staff_id', $staff->id);
            return [
                'fullname' => $staff->fullname,
                'appointments'=>$appointments,
                'status' => !$staffAppointments->isEmpty()
            ];
        });

        return response()->json($availability, 200);
    } catch (\Exception $error) {
        return response()->json(['error' => $error->getMessage()], 500);
    }
}

// update Appointment
    public function updateAppointment(Request $request , $id){
        try{
        $appointment = Appointments::find($id);
        if(is_null($appointment)){
            return response()->json(['message'=>'The Appointment cannot be find']);
        }else{
            // $customer = Customers::find($id);
            // $staff = Staffs::find($id);
            // $service = Services::find($id);

            

            $appointment->update([

                // 'customer_id' => $request->input('customer_id'),
                'staff_id' =>  $request->input('staff_id'),
                'service_id' =>  $request->input('service_id'),
                'date' => Carbon::createFromFormat('Y-m-d', $request->input('date'))->format('Y-m-d'),
                'time' => Carbon::createFromFormat('H:i', $request->input('time'))->format('H:i:s'),
                'price'=>$request->input('price')

            ]);

            $invoice = $appointment->invoice; // Assuming you have a relationship defined between appointment and invoice

            // Update the invoice if it exists
            if ($invoice) {
                $invoice->update([
                    'service_name' => $appointment->service->service_name,
                    'total_amount' => $appointment->price,
                    'due_date' => $appointment->date, // Keep the existing due date or update it if necessary
                    
                ]);
            }

            return response()->json(['message'=>'successfully updated']);
        }
    }catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()]);
    }

  }

  //function for updating time in update appointment

    public function getUnavailableTimeSlots(Request $request){

        try{
            $date = $request->input('date');

            $unavailableSlots = Appointments::where('date',$date)->pluck('time')->toArray();
            return response()->json(['unavailableSlots'=> $unavailableSlots]);

        }catch(\Exception $error){
            return response()->json(['error'=>$error->getMessage()],500);

        }

    }
  
  //get Appointment by id

  public function getAppointmentById($id){
    try {
        
        $appointment = Appointments::with(['customer', 'staff', 'service'])->find($id);

        
        if (is_null($appointment)) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

       
        $formattedAppointment = [
            'appointment' => [
                'id' => $appointment->id,
                'date' => $appointment->date,
                'time' => $appointment->time,
            ],
            'customer' => [
                'id' => $appointment->customer->id,
                'fullname' => $appointment->customer->fullname,
                'email' => $appointment->customer->email,
                'gender' => $appointment->customer->gender,
                'address' => $appointment->customer->address,
                'contact_no' => $appointment->customer->contact_no,
            ],
            'staff' => [
                'id' => $appointment->staff->id,
                'fullname' => $appointment->staff->fullname,
                'email' => $appointment->staff->email,
                'contact_no' => $appointment->staff->contact_no,
            ],
            'service' => [
                'id' => $appointment->service->id,
                'service_name' => $appointment->service->service_name,
                'duration'=>$appointment->service->duration,
                'price'=>$appointment->service->price,
            ],
        ];

        return response()->json(['appointment' => $formattedAppointment], 200);
    } catch (\Exception $error) {
        return response()->json(['error' => $error->getMessage()], 500);
    }
}
  }