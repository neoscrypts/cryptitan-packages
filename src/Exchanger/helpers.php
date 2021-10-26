<?php

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Illuminate\Support\Facades\App;
use NeoScrypts\Exchanger\Exchanger;

if (!function_exists('exchanger')) {
    /**
     * Convert money
     *
     * @param Money|null $money
     * @param Currency|null $toCurrency
     * @return Money|Exchanger
     */
    function exchanger(Money $money = null, Currency $toCurrency = null)
    {
        if (is_null($money)) {
            return App::make('exchanger');
        }

        return App::make('exchanger')->convert($money, $toCurrency);
    }
}

if (!function_exists('convertCurrency')) {
    /**
     * Convert currency
     *
     * @param $amount
     * @param string $from
     * @param string $to
     * @param bool $format
     * @return float|string
     */
    function convertCurrency($amount, string $from, string $to, bool $format = true)
    {
        $money = new Money($amount, new Currency($from), true);
        $converted = App::make('exchanger')->convert($money, new Currency($to));
        return $format ? $converted->format() : $converted->getValue();
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency
     *
     * @param $amount
     * @param string $currency
     * @return string
     */
    function formatCurrency($amount, string $currency)
    {
        $money = new Money($amount, new Currency($currency), true);
        return $money->format();
    }
}
