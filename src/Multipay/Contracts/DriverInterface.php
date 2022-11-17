<?php

namespace NeoScrypts\Multipay\Contracts;

use NeoScrypts\Multipay\Order;

interface DriverInterface
{
    /**
     * Get driver name
     *
     * @return string
     */
    public function getName();

    /**
     * Set driver name
     *
     * @param $name
     * @return void
     */
    public function setName($name);

    /**
     * Create new purchase
     *
     * @param Order $order
     * @param callable $callback
     * @return mixed
     */
    public function request(Order $order, $callback);

    /**
     * verify the payment
     *
     * @return bool
     */
    public function verify($transactionId);

    /**
     * Handle return action
     *
     * @param array $data
     * @return string
     */
    public function handleReturn(array $data): string;

    /**
     * Handle notify action
     *
     * @param array $data
     * @return string
     */
    public function handleNotify(array $data): string;

    /**
     * Check if driver supports currency
     *
     * @param string $currency
     * @return mixed
     */
    public function supportsCurrency(string $currency): bool;
}