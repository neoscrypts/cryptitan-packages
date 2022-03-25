<?php

namespace NeoScrypts\Multipay\Drivers;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use NeoScrypts\Multipay\Order;

class MidtransDriver extends AbstractDriver
{
    const DRIVER_NAME = "Midtrans";

    /**
     * Initialize Stripe instance
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        Config::$isProduction = $this->config('production');
        Config::$serverKey = $this->config('server_key');
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function request(Order $order, $callback)
    {
        $response = Snap::createTransaction($this->buildRequest($order));

        return $callback($order->getUuid(), $response->redirect_url);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function verify($transactionId)
    {
        $result = Transaction::status($transactionId);

        return $result->transaction_status === "capture" &&
            $result->fraud_status === "accept";
    }

    /**
     * @inheritDoc
     */
    public function supportsCurrency(string $currency): bool
    {
        return strtoupper($currency) === "IDR";
    }

    /**
     * Build request body
     *
     * @param Order $order
     * @return array
     */
    protected function buildRequest(Order $order)
    {
        return [
            'transaction_details' => [
                'order_id'     => $order->getUuid(),
                'gross_amount' => round($order->getTotalAmount()->getValue()),
            ],
            'callbacks'           => [
                'finish' => $this->callbackUrl($order, ['status' => 'success'])
            ],
        ];
    }
}