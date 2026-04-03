<?php
// config/site.php

return [
    'name' => env('APP_NAME', 'Restaurant Site Name'),
    'email' => env('SITE_EMAIL', 'palombinicoffee@gmail.com'),
    'url' => env('APP_URL', 'http://localhost'),
    'address' => env('SITE_ADDRESS', '9 Abd El-Khalik Tharwat, San Stefano, El Raml 1, Alexandria Governorate 5452055'),
    'phone' => env('SITE_PHONE', '01501553116'),
    'google_maps_link' => env('SITE_GOOGLE_MAPS_LINK', 'https://maps.app.goo.gl/omVGRkThowQbxXmH9'),
    'country' => 'Egypt',
    'currency_symbol' => 'LE',
    'currency_code' => 'EGP',
];
