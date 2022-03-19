<?php

use NeoScrypts\Multipay\Drivers\PaypalDriver;
use NeoScrypts\Multipay\Drivers\StripeDriver;

return [

    /*
     |--------------------------------------------------------------------------
     | Default Gateway
     |--------------------------------------------------------------------------
     |
     | This value determines which of the following gateway to use.
     | You can switch to a different driver at runtime.
     |
     */
    'default' => 'paypal',

    /*
    |--------------------------------------------------------------------------
    | List of Gateways
    |--------------------------------------------------------------------------
    |
    | These are the driver configurations available
    |
    */

    'gateways' => [
        'paypal' => [
            'driver'        => PaypalDriver::class,
            'enable'        => (bool) env('PAYPAL_ENABLE', true),
            'client_env'    => env('PAYPAL_CLIENT_ENV', 'live'),
            'client_id'     => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET')
        ],

        'stripe' => [
            'driver'        => StripeDriver::class,
            'enable'        => (bool) env('STRIPE_ENABLE', false),
            'client_key'    => env('STRIPE_KEY'),
            'client_secret' => env('STRIPE_SECRET'),
        ]
    ]
];