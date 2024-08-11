<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminders extends Model

{
    protected $table = 'reminders';
    protected $primaryKey = 'id';
    protected $fillable = ['type','message','is_read','staff_id','appointment_id','send_at'];
    use HasFactory;

    public function appointment(){
        return $this->belongsTo(Appointments::class);
       }

    public function staff(){
        return $this->belongsTo(Staffs::class);
    }   
}
