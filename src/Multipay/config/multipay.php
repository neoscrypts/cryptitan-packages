<?php

use NeoScrypts\Multipay\Drivers\PaypalDriver;

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
            'callback'      => 'gateway-callback.paypal',
            'client_env'    => env('PAYPAL_CLIENT_ENV', 'live'),
            'client_id'     => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET')
        ]
    ]
];