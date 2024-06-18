<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    protected $table = 'staffs';
    protected $primaryKey = 'id';
    protected $fillable = ['fullname','email','contact_no','dob','role','status','username','password'];
    use HasFactory;

    public function appointments()
    {
        return $this->hasMany(Appointments::class);
    }
}
