<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Customers;
use App\Models\Invoices;
use App\Models\Services;
use App\Models\Staffs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // public function retrieveData(){
    //    $appointment = Appointments::all();
    //    try{
    //     return response()->json(['status'=>true, $appointment],200);
    //    }catch(\Exception $error){
    //     return response()->json(['error'=>$error->getMessage()],500);
    //    }
    // }

    // public function addCustomerDetails(Request $request){
    //     try{
    //     $customer = Customers::where('contact_no', $request->input('contact_no'))->first();
    //     if(is_null($customer)){
    //         return response()->json(['message'=>'we could not find any customer on this number']);
    //     }else{
    //         return response()->json(['customer'=>$customer]);

    //     }
    // } catch(\Exception $error){
    //     return response()->json(['message'=>$error->getMessage()],500);
    // }  
    // }

    // public function addAppointment(Request $request){

    //     $customerName = Customers::pluck('fullname', $request->input('fullname'))->first();
    //     //   return response()->json(['message'=>true ,$customerName]);

    //     $staffName = Staffs::pluck('fullname', $request->input('fullname'))->first();
    //     $serviceName = Services::where('service_name', $request->input('service_name'))->first();
    //     // return response()->json(['message'=>true ,$serviceName]);

    //     try{ 
    //         if (!$customerName) {
    //             return response()->json(['message' => 'Customer was already exist', 'debug' => $request->input('customer_fullname')], 400);
    //         }
    //         if (!$staffName) {
    //             return response()->json(['message' => 'Staff not found', 'debug' => $request->input('staff_fullname')], 400);
    //         }
    //         if (!$serviceName) {
    //             return response()->json(['message' => 'Service not found', 'debug' => $request->input('service_name')], 400);
    //         }
    //        else{
    //         $appointment = new Appointments([
    //             "customer_name"=>$customerName,
    //             "staff_name"=>$staffName,
    //             "service_name"=>$serviceName->service_name,
    //             'date'=> $request->input('date'),
    //             'time'=> $request->input('time')->format('h.i a')
    //         ]);
    //         $appointment->save();
    //         return response()->json(['message' => 'Sucessfully Added'],200);
    //     }
    //     }
    //     catch(\Exception $error){
    //         return response()->json(['message'=>$error->getMessage()],500);
    //     }
    // }


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
            return response()->json(['message'=>'we could not find any customer on this number']);
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

        // $customer = Customers::firstOrCreate([
        //     'contact_no'=>$request->input('contact_no')
        // ],
        // [
        //     'fullname'=>$request->input('fullname'),
        //     'email'=>$request->input('email'),
        //     'gender'=>$request->input('gender'),
        //     'address'=>$request->input('address')
        // ]
        // );
        $customer = Customers::findOrFail($request->input('customer_id'));

        //validate the staff

           $staff = Staffs::findOrFail($request->input('staff_id'));

           $serviceIds = $request->input('service_id');


           if (empty($serviceIds) || !is_array($serviceIds)) {
            return response()->json(['error' => 'Invalid service IDs'], 400);
        }


        foreach ($serviceIds as $serviceId) {
            $service = Services::where('service_name', $serviceId)->first();

            if (!$service) {
                return response()->json(['error' => "Service not found: $serviceId"], 400);
            }
        

                $validateAppointment = Appointments::where('staff_id', $staff->id)->
                where('service_id', $service->id)
                ->where('date', $request->input('date'))
                ->where('time', $request->input('time'))
                ->first();

    
        if($validateAppointment){
            return response()->json(['message'=>'This time slot is already booked']);
        }

        //create a new appointment

        $appointment = new Appointments([
            'customer_id'=>$customer->id,
            'staff_id'=>$staff->id,
            'service_id'=>$service->id,
            'date'=>$request->input('date'),
            'time'=>$request->input('time')
        ]);
        $appointment->save();
    }
        return response()->json(['message'=>'successfully added'],200);
    

    }catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
}

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

    // Get all appointments for the given date
    $appointments = Appointments::with('staff')->where('date', $date)->get();

    // Predefined list of time slots
    $allTimeSlots = [
        '09:00', '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00', '17:00', '18:00'
    ];

    foreach ($allTimeSlots as $time) {
        $bookedAppointment = $appointments->firstWhere('time', $time);
        if ($bookedAppointment) {
            $timeSlots[] = [
                'time' => $time,
                'isBooked' => true,
                'staffName' => $bookedAppointment->staff->fullname
            ];
        } else {
            $timeSlots[] = [
                'time' => $time,
                'isBooked' => false,
                'staffName' => ''
            ];
        }
    }

    return response()->json(['timeSlots' => $timeSlots], 200);
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
        $appointments = Appointments::whereIn('staff_id', $staffs->pluck('id'))
                                    ->where('date', '>=', $date)
                                    ->get();

        $availability = $staffs->map(function($staff) use ($appointments) {
            $staffAppointments = $appointments->where('staff_id', $staff->id);
            return [
                'fullname' => $staff->fullname,
                'status' => $staffAppointments->isEmpty()
            ];
        });

        return response()->json($availability, 200);
    } catch (\Exception $error) {
        return response()->json(['error' => $error->getMessage()], 500);
    }
}

// update Appointment
    public function updateAppointment($id){
        try{
        $appointment = Appointments::find($id);
        if(is_null($appointment)){
            return response()->json(['message'=>'The Appointment cannot be find']);
        }else{
            $appointment->update([

                    'cusName' => $appointment->customer->fullname,
                    'empName' => $appointment->staff->fullname,
                    'service' => $appointment->service->service_name,
                    'date' => $appointment->date,
                    'time' => $appointment->time

            ]);
            return response()->json(['message'=>'successfully updated']);
        }
    }catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
  }

  //create both appointment and invoices

  public function appointmentWithInvoice(Request $request){
     $appointment = Appointments::create([
            'customer_id'=>$request->customer_id,
            'staff_id'=>$request->staff_id,
            'service_id'=>$request->service_id,
            'date'=>$request->date,
            'time'=>$request->time
     ]);

     $invoice = Invoices::create([
        'appointment_id'=>$appointment->id,
        'customer_name'=>$appointment->customer->fullname,
        'total_amount'=>$request->price,
        'issue_date'=>today(),
        'due_date'=>$appointment->date
     ]);

     return response()->json(['message'=>'successfull added both appointment and invoice',
     'appointment'=>$appointment,
     'invoice'=>$invoice
    ]);
  }


}