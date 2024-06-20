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
}
