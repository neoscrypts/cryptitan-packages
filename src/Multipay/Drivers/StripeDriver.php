<?php

namespace NeoScrypts\Multipay\Drivers;

use NeoScrypts\Multipay\Order;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeDriver extends AbstractDriver
{
    const DRIVER_NAME = "Stripe";

    /**
     * Supported currency codes
     *
     * @var string[]
     */
    protected static $supportedCurrencies = [
        "USD", "AED", "AFN",
        "ALL", "AMD", "ANG",
        "AOA", "ARS", "AUD",
        "AWG", "AZN", "BAM",
        "BBD", "BDT", "BGN",
        "BMD", "BND", "BOB",
        "BRL", "BSD", "BWP",
        "BYN", "BZD", "CAD",
        "CDF", "CHF", "CNY",
        "COP", "CRC", "CVE",
        "CZK", "DKK", "DOP",
        "DZD", "EGP", "ETB",
        "EUR", "FJD", "FKP",
        "GBP", "GEL", "GIP",
        "GMD", "GTQ", "GYD",
        "HKD", "HNL", "HRK",
        "HTG", "HUF", "IDR",
        "ILS", "INR", "ISK",
        "JMD", "KES", "KGS",
        "KHR", "KYD", "KZT",
        "LAK", "LBP", "LKR",
        "LRD", "LSL", "MAD",
        "MDL", "MKD", "MMK",
        "MNT", "MOP", "MRO",
        "MUR", "MVR", "MWK",
        "MXN", "MYR", "MZN",
        "NAD", "NGN", "NIO",
        "NOK", "NPR", "NZD",
        "PAB", "PEN", "PGK",
        "PHP", "PKR", "PLN",
        "QAR", "RON", "RSD",
        "RUB", "SAR", "SBD",
        "SCR", "SEK", "SGD",
        "SHP", "SLL", "SOS",
        "SRD", "STD", "SZL",
        "THB", "TJS", "TOP",
        "TRY", "TTD", "TWD",
        "TZS", "UAH", "UYU",
        "UZS", "WST", "XCD",
        "YER", "ZAR", "ZMW"
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::DRIVER_NAME;
    }

    /**
     * @inheritDoc
     * @throws ApiErrorException
     */
    public function request(Order $order, $callback)
    {
        $request = $this->buildRequest($order);

        $session = $this->getClient()->checkout->sessions->create($request);

        return $callback($session->id, $session->url);
    }

    /**
     * @inheritDoc
     * @throws ApiErrorException
     */
    public function verify($transactionId)
    {
        $session = $this->getClient()->checkout->sessions->retrieve($transactionId);

        return $session->payment_status === "paid";
    }

    /**
     * @inheritDoc
     */
    public function supportsCurrency(string $currency): bool
    {
        return $this->config('enable') && in_array(strtoupper($currency), self::$supportedCurrencies);
    }

    /**
     * Build request body
     *
     * @param Order $order
     * @return array
     */
    protected function buildRequest(Order $order)
    {
        $amount = $order->getTotalAmount();

        return [
            'line_items'  => [[
                'price_data' => [
                    'product_data' => ['name' => 'Payment'],
                    'currency'     => strtolower($amount->getCurrency()->getCurrency()),
                    'unit_amount'  => $amount->getAmount(),
                ],
                'quantity'   => 1
            ]],
            'success_url' => $this->callbackUrl($order, ['status' => 'success']),
            'cancel_url'  => $this->callbackUrl($order, ['status' => 'cancel']),
            'mode'        => 'payment',
        ];
    }

    /**
     * Get stripe client
     *
     * @return StripeClient
     */
    protected function getClient()
    {
        return new StripeClient($this->config('client_key'));
    }
}