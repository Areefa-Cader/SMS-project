<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create owner details

        User::create([
            'fullname' => 'Rinoshas Bridal',
            'email' => 'Rinoshasbridal@gmail.com',
            'contact_number'=> '0764334455',
            'role'=>'owner',
            'username'=>'SalonRinosha',
            'password'=>Hash::make('Rinosha@2024'),
            'status'=>'active'
        ]);

        //create admin details

        User::create([
            'fullname' => 'Admin user',
            'email' => 'admin@gmail.com',
            'contact_number'=> '0772382178',
            'role'=>'admin',
            'username'=>'Adminuser',
            'password'=>Hash::make('Admin@2024'),
            'status'=>'active'
        ]);
    }
}
