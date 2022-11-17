<?php

namespace NeoScrypts\Multipay\Drivers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use NeoScrypts\Multipay\Contracts\DriverInterface;
use NeoScrypts\Multipay\Order;

abstract class AbstractDriver implements DriverInterface
{
    const SUCCESS = 'success';
    const REDIRECT = 'redirect';
    const FAILURE = 'failure';

    /**
     * Driver name
     *
     * @var string
     */
    protected $name;

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
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function handleReturn(array $data): string
    {
        return static::REDIRECT;
    }

    /**
     * @inheritDoc
     */
    public function handleNotify(array $data): string
    {
        return static::SUCCESS;
    }

    /**
     * Get return url
     *
     * @param Order $order
     * @param array $params
     * @return string
     */
    protected function returnUrl(Order $order, array $params = [])
    {
        $params = array_merge(['order' => $order->getUuid()], $params);

        return URL::route("gateway.return", $params);
    }

    /**
     * Get notify url
     *
     * @param Order $order
     * @param array $params
     * @return string
     */
    protected function notifyUrl(Order $order, array $params = [])
    {
        $params = array_merge(['order' => $order->getUuid()], $params);

        return URL::route("gateway.notify", $params);
    }
}