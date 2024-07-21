<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Staffs extends Model implements JWTSubject
{
    protected $table = 'staffs';
    protected $primaryKey = 'id';
    protected $fillable = ['fullname','email','contact_no','dob','role','status','username','password'];
    use HasFactory, HasApiTokens;

    public function appointments()
    {
        return $this->hasMany(Appointments::class);
    }

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }  
}
