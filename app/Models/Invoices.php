<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
   protected $fillable = ['appointment_id','customer_name','total_amount','issue_date','due_date','status'];

   public function appointment(){
    return $this->belongsTo(Appointments::class);
   }
}
