<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service_Reports extends Model
{
    protected $fillable = ['appointment_id','service_name','service_category','service_price','staff_name','start_date','end_date'];

    public function appointment(){
     return $this->belongsTo(Appointments::class);
    }
    use HasFactory;
}
