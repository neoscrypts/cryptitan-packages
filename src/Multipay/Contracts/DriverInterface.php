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
    public function getName(): string;

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
     * Check if driver supports currency
     *
     * @param string $currency
     * @return mixed
     */
    public function supportsCurrency(string $currency): bool;
}