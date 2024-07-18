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
            'email' => 'Rinosha12@gmail.com',
            'contact_number'=> '0764334455',
            'role'=>'owner',
            'username'=>'Salon_Rinosha',
            'password'=>Hash::make('Rinosha@12mua'),
            'status'=>'active'
        ]);

        //create admin details

        User::create([
            'fullname' => 'John Doe',
            'email' => 'john27@gmail.com',
            'contact_number'=> '0772382178',
            'role'=>'admin',
            'username'=>'Admin',
            'password'=>Hash::make('User27admin$'),
            'status'=>'active'
        ]);
    }
}
