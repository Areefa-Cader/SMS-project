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

}
