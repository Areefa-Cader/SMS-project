<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
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
            $invoice = Invoices::find($id);
            if(is_null($invoice)){
                return response()->json(['error'=>'Invoice is not found']);
            }else{
                $invoice->update([
                    'customer_name' => $request->input('customer_name'),
                    'due_date' => $request->input('due_date'),
                    'advance_payment' => $request->input('advance_payment'),
                    'status' => $request->input('status'),
                ]);

                return response()->json(['message'=>'Successfully Updated']);
                
            }
        }catch(\Exception $error){
            return response()->json(['error'=>$error->getMessage()]);
        }
    }

}
