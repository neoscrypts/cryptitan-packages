<?php

namespace NeoScrypts\Multipay;

use Illuminate\Support\Arr;

class Multipay
{
    /**
     * Multipay configuration.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Multipay gateway instance.
     *
     * @var Contracts\DriverInterface
     */
    protected $gateway;

    /**
     * Create a new instance.
     *
     * @param array $config
     * @param string|null $gateway
     */
    public function __construct(array $config, string $gateway = null)
    {
        $this->config = tap($config, function ($config) use ($gateway) {
            $gateway = $gateway ?: Arr::get($config, 'default');
            $driverConfig = Arr::get($config, 'gateways.' . $gateway, []);
            $driver = Arr::pull($driverConfig, 'driver');
            $this->gateway = new $driver($driverConfig);
        });
    }

    /**
     * Set gateway
     *
     * @param $gateway
     * @return $this
     */
    public function gateway($gateway)
    {
        return new static($this->config, $gateway);
    }

    /**
     * Request payment
     *
     * @param Order $order
     * @param $callback
     * @return mixed
     */
    public function request(Order $order, $callback)
    {
        return $this->gateway->request($order, $callback);
    }

    /**
     * Verify transaction
     *
     * @param $transactionId
     * @return bool
     */
    public function verify($transactionId)
    {
        return $this->gateway->verify($transactionId);
    }

    /**
     * Check if gateway supports currency
     *
     * @param string $currency
     * @return bool|mixed
     */
    public function supportsCurrency(string $currency)
    {
        return $this->gateway->supportsCurrency($currency);
    }
}