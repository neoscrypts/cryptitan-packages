<?php

namespace NeoScrypts\Exchanger;

use Akaunting\Money\Currency;
use Illuminate\Support\Arr;
use Akaunting\Money\Money;
use NeoScrypts\Exchanger\Drivers\AbstractDriver;
use OutOfBoundsException;
use UnexpectedValueException;

class Exchanger
{
    /**
     * Exchanger configuration.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Exchanger driver instance.
     *
     * @var AbstractDriver
     */
    protected AbstractDriver $driver;

    /**
     * Create a new instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = tap($config, function ($config){
            $baseCurrency = Arr::get($config, 'base_currency');
            $driverName = Arr::get($config, 'default');
            $driverConfig = Arr::get($config, "drivers.$driverName", []);
            $driver = Arr::pull($driverConfig, 'class');
            $this->driver = new $driver($baseCurrency, $driverConfig);
        });
    }

    /**
     * Convert money.
     *
     * @param Money $money
     * @param Currency $toCurrency
     *
     * @return Money
     */
    public function convert(Money $money, Currency $toCurrency)
    {
        $baseCurrency = $money->getCurrency();

        if ($baseCurrency->equals($toCurrency)) {
            return $money;
        }

        $ratio = $this->getExchangeRate($toCurrency) / $this->getExchangeRate($baseCurrency);

        return $money->convert($toCurrency, $ratio);
    }


    /**
     * Get storage driver.
     *
     * @return AbstractDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get exchange rate
     *
     * @param Currency $currency
     *
     * @return float
     */
    protected function getExchangeRate(Currency $currency): float
    {
        $code = $currency->getCurrency();

        if (!is_array($data = $this->getDriver()->find($code))) {
            throw new OutOfBoundsException("$code exchange rate does not exists.");
        }

        $exchangeRate = Arr::get($data, 'exchange_rate');

        if (is_null($exchangeRate)) {
            throw new UnexpectedValueException("$code exchange rate is not available.");
        }

        return $exchangeRate;
    }

    /**
     * Get configuration value.
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function config(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }
}