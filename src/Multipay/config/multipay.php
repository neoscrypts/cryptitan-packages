<?php

use NeoScrypts\Multipay\Drivers\MidtransDriver;
use NeoScrypts\Multipay\Drivers\MollieDriver;
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

        'mollie' => [
            'driver'     => MollieDriver::class,
            'enable'     => (bool) env('MOLLIE_ENABLE', false),
            'client_key' => env('MOLLIE_CLIENT_KEY')
        ],

        'payu' => [
            'driver'        => PayUDriver::class,
            'enable'        => (bool) env('PAYU_ENABLE', false),
            'client_env'    => env('PAYU_CLIENT_ENV', 'secure'),
            'client_id'     => env('PAYU_CLIENT_ID'),
            'client_secret' => env('PAYU_CLIENT_SECRET'),
            'currency'      => env('PAYU_CURRENCY', 'PLN'),
        ],

        'midtrans' => [
            'driver'     => MidtransDriver::class,
            'enable'     => (bool) env('MIDTRANS_ENABLE', false),
            'production' => (bool) env('MIDTRANS_PRODUCTION', true),
            'server_key' => env('MIDTRANS_SERVER_KEY'),
        ],
    ]
];