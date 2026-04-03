<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestaurantPhoneNumber;

class RestaurantPhoneNumberSeeder extends Seeder
{
    public function run(): void
    {
        RestaurantPhoneNumber::query()->delete();

        RestaurantPhoneNumber::create([
            'phone_number' => '01501553116',
            'use_whatsapp' => 1,
        ]);
    }
}

// php artisan db:seed --class=RestaurantPhoneNumberSeeder
