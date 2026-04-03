<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyAddress;

class CompanyAddressSeeder extends Seeder
{
    public function run(): void
    {
        CompanyAddress::query()->delete();

        CompanyAddress::create([
            'street'       => '9 Abd El-Khalik Tharwat, San Stefano, El Raml 1',
            'city'         => 'Alexandria',
            'state'        => 'Alexandria Governorate',
            'postal_code'  => '5452055',
            'country'      => 'Egypt',
            'latitude'     => null,
            'longitude'    => null,
        ]);
    }
}


// php artisan db:seed --class=CompanyAddressSeeder
