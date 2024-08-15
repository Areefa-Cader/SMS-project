<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Invoices;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function getAllInvoice(){
        $invoice = Invoices::with('appointment.customer')->get();
        return response()->json($invoice);
    }

    public function getInvoiceById($id){
        try{
        $invoice = Invoices::find($id);
        if(is_null($invoice)){
            return response()->json(['message'=>'invoice is not found'],404);
        }
        return response()->json(['invoice'=>$invoice],200);
    }catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
    }

    public function updateInvoice(Request $request, $id){
        try{

            // $invoice = Invoices::where('appointment_id', $id)->first();
            $invoice = Invoices::find($id);
            if(is_null($invoice)){
                return response()->json(['error'=>'Invoice is not found']);
            }else{
                $invoice->update([
                    'service_name' =>  $request->input('service_name'),
                    'date' => Carbon::createFromFormat('Y-m-d', $request->input('date'))->format('Y-m-d'),
                    'time' => Carbon::createFromFormat('H:i', $request->input('time'))->format('H:i:s'),
                    'price'=>$request->input('price') 
                ]);
            }
                return response()->json(['message'=>'Successfully Updated']);
                
            }
    catch(\Exception $error){
            return response()->json(['error'=>$error->getMessage()]);
        }
    }

    public function getInvoiceByAppointmentId($appointmentId) {
        $invoice = Invoices::where('appointment_id', $appointmentId)->first();
        if ($invoice) {
            return response()->json(['exists' => true, 'invoice' => $invoice]);
        } else {
            return response()->json(['exists' => false]);
        }
    }
    

}
