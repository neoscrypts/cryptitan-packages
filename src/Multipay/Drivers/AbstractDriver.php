<?php

namespace NeoScrypts\Multipay\Drivers;

use Illuminate\Support\Arr;
use NeoScrypts\Multipay\Contracts\DriverInterface;
use NeoScrypts\Multipay\Order;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * Driver config
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get configuration value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function config(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Get callback url
     *
     * @param Order $order
     * @param array $params
     * @return string
     */
    protected function callbackUrl(Order $order, array $params = [])
    {
        $params = array_merge([
            'order' => $order->getUuid()
        ], $params);

        return route($this->config('callbackRoute'), $params);
    }
}