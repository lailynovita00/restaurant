<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use Carbon\Carbon;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'user_id'     => 3,
            'label'       => 'delivery',
            'street'      => '12 El Geish Road',
            'city'        => 'Alexandria',
            'state'       => 'Alexandria Governorate',
            'postal_code' => '21500',
            'country'     => 'Egypt',
            'is_default'  => true,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        Address::create([
            'user_id'     => 3,
            'label'       => 'delivery',
            'street'      => '45 Abu Qir Street',
            'city'        => 'Alexandria',
            'state'       => 'Alexandria Governorate',
            'postal_code' => '21615',
            'country'     => 'Egypt',
            'is_default'  => false,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
}


//php artisan db:seed --class=AddressSeeder
