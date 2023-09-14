<?php

use NeoScrypts\Multipay\Drivers\CinetPayDriver;
use NeoScrypts\Multipay\Drivers\MollieDriver;
use NeoScrypts\Multipay\Drivers\PaystackDriver;
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
    'default' => 'stripe',

    /*
    |--------------------------------------------------------------------------
    | List of Gateways
    |--------------------------------------------------------------------------
    |
    | These are the driver configurations available
    |
    */

    'gateways' => [
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

        'paystack' => [
            'driver'        => PaystackDriver::class,
            'enable'        => (bool) env('PAYSTACK_ENABLE', false),
            'client_secret' => env('PAYSTACK_CLIENT_SECRET')
        ],

        'payu' => [
            'driver'        => PayUDriver::class,
            'enable'        => (bool) env('PAYU_ENABLE', false),
            'currency'      => env('PAYU_CURRENCY', 'PLN'),
            'client_env'    => env('PAYU_CLIENT_ENV', 'secure'),
            'client_id'     => env('PAYU_CLIENT_ID'),
            'client_secret' => env('PAYU_CLIENT_SECRET'),
        ],

        'cinetpay' => [
            'driver'  => CinetPayDriver::class,
            'enable'  => (bool) env('CINETPAY_ENABLE', false),
            'apikey'  => env('CINETPAY_APIKEY'),
            'site_id' => env('CINETPAY_SITE_ID'),
        ]
    ]
];