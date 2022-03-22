<?php

use NeoScrypts\Multipay\Drivers\PaypalDriver;
use NeoScrypts\Multipay\Drivers\PayUDriver;
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
            'driver'     => StripeDriver::class,
            'enable'     => (bool) env('STRIPE_ENABLE', false),
            'client_key' => env('STRIPE_KEY'),
        ],

        'payu' => [
            'driver'        => PayUDriver::class,
            'enable'        => (bool) env('PAYU_ENABLE', false),
            'currency'      => env('PAYU_CURRENCY', 'PLN'),
            'client_env'    => env('PAYU_CLIENT_ENV', 'secure'),
            'client_id'     => env('PAYU_CLIENT_ID'),
            'client_secret' => env('PAYU_CLIENT_SECRET'),
        ],
    ]
];