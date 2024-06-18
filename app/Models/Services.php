<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $fillable = ['service_name','service_category','duration','price'];
    use HasFactory;

    public function appointments()
    {
        return $this->hasMany(Appointments::class);
    }
}
