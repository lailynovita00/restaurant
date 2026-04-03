<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
            ->where('email', 'chrysanthusobinna@gmail.com')
            ->delete();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@palombini.com'],
            [
                'first_name' => 'admin',
                'middle_name' => null,
                'last_name' => 'palombini',
                'password' => Hash::make('12345678'), // Hashed password
                'role' => 'global_admin',
                'status' => 1,
                'phone_number' => '01501553116',
                'address' => '9 Abd El-Khalik Tharwat, San Stefano, El Raml 1, Alexandria Governorate 5452055',
                'profile_picture' => null, // Default null if no picture
                'activation_token' => null, // Default null if no activation token
                'remember_token' => null,
                'two_factor_auth' => 0,
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'cashier@palombini.com'],
            [
                'first_name' => 'cashier',
                'middle_name' => null,
                'last_name' => 'palombini',
                'password' => Hash::make('12345678'),
                'role' => 'cashier',
                'status' => 1,
                'phone_number' => '+440000000000',
                'address' => 'Palombini Cafe, Alexandria, Egypt',
                'profile_picture' => null,
                'activation_token' => null,
                'remember_token' => null,
                'two_factor_auth' => 0,
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}


// php artisan db:seed --class=UserSeeder
