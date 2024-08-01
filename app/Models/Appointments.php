<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id','staff_id','service_id','date','time'];

    public function customer(){
        return $this->belongsTo(Customers::class);
    }

    public function staff() {
        return $this->belongsTo(Staffs::class);
    }

    public function service() {
        return $this->belongsTo(Services::class );
    }

    public function invoice(){
        return $this->belongsTo(Invoices::class);
    }

    use HasFactory;
}
