<?php

return [
    'provider' => env('PAYMENT_PROVIDER', 'stripe'),

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'public' => env('STRIPE_PUBLIC'),
    ],

    'paystack' => [
        'secret' => env('PAYSTACK_SECRET'),
        'public' => env('PAYSTACK_PUBLIC'),
    ],

    'manual_transfer' => [
        'instapay_number' => env('MANUAL_INSTAPAY_NUMBER', '0100 111 2222'),
        'vodafone_cash_number' => env('MANUAL_VODAFONE_CASH_NUMBER', '0100 333 4444'),
    ],
];
