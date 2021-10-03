<?php

namespace NeoScrypts\Multipay\Drivers;

use Akaunting\Money\Money;
use Illuminate\Support\Collection;
use LogicException;
use NeoScrypts\Multipay\Order;
use NeoScrypts\Multipay\OrderItem;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class PaypalDriver extends AbstractDriver
{
    /**
     * Paypal client
     *
     * @var PayPalHttpClient
     */
    protected PayPalHttpClient $client;

    /**
     * Supported currency codes
     *
     * @var string[]
     */
    protected static array $supportedCurrencies = [
        'AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF',
        'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN',
        'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD'
    ];

    /**
     * Initialize Paypal instance
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if ($config['client_env'] === 'live') {
            $env = new ProductionEnvironment(
                $config['client_id'], $config['client_secret']
            );
        } else {
            $env = new SandboxEnvironment(
                $config['client_id'], $config['client_secret']
            );
        }

        $this->client = new PayPalHttpClient($env);
    }

    /**
     * Request payment
     *
     * @param Order $order
     * @param callable $callback
     * @return mixed
     * @throws \PayPalHttp\HttpException
     * @throws \PayPalHttp\IOException
     */
    public function request(Order $order, $callback)
    {
        $request = new OrdersCreateRequest();
        $request->body = $this->buildRequestBody($order);
        $response = $this->client->execute($request);
        $result = $this->parseResult($response->result);

        $approveLink = collect($result->get('links'))->firstWhere('rel', 'approve');

        if (is_array($approveLink)) {
            return $callback($result->get('id'), $approveLink['href']);
        }

        throw new LogicException('Request failed');
    }

    /**
     * Verify payment
     *
     * @param $transactionId
     * @return bool
     * @throws \PayPalHttp\HttpException
     * @throws \PayPalHttp\IOException
     */
    public function verify($transactionId)
    {
        $request = new OrdersGetRequest($transactionId);
        $response = $this->client->execute($request);
        $result = $this->parseResult($response->result);

        if ($result->get('status') === 'APPROVED') {
            $captureRequest = new OrdersCaptureRequest($transactionId);
            $captureResponse = $this->client->execute($captureRequest);

            $result = $this->parseResult($captureResponse->result);
        }

        return $result->get('status') === 'COMPLETED';
    }

    /**
     * Check if gateway supports currency
     *
     * @param string $currency
     * @return bool
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
    protected function buildRequestBody(Order $order)
    {
        $paymentUnit = [
            'amount'    => $this->getMoneyObject($order->getTotalAmount()),
            'custom_id' => $order->getUuid()
        ];

        if (!$order->isFixed()) {
            $breakdown = [
                'item_total' => $this->getMoneyObject($order->getSubTotal()),
            ];

            if (($tax = $order->getTotalTax()) && !$tax->isZero()) {
                $breakdown['tax_total'] = $this->getMoneyObject($tax);
            }

            if ($shipping = $order->getShipping()) {
                $breakdown['shipping'] = $this->getMoneyObject($shipping);
            }

            if ($handling = $order->getHandling()) {
                $breakdown['handling'] = $this->getMoneyObject($handling);
            }

            if ($discount = $order->getDiscount()) {
                $breakdown['discount'] = $this->getMoneyObject($discount);
            }

            $paymentUnit['amount']['breakdown'] = $breakdown;

            $paymentUnit['items'] = $order->collectItems()
                ->map(function (OrderItem $item) {
                    $body = [
                        'name'        => $item->getName(),
                        'unit_amount' => $this->getMoneyObject($item->getUnitPrice()),
                        'quantity'    => $item->getQuantity(),
                    ];

                    if ($description = $item->getDescription()) {
                        $body['description'] = $description;
                    }

                    if ($tax = $item->getUnitTax()) {
                        $body['tax'] = $this->getMoneyObject($tax);
                    }
                    return $body;
                })->toArray();
        }

        return [
            'intent'              => 'CAPTURE',
            'application_context' => [
                'brand_name'          => config('app.name'),
                'user_action'         => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
                'return_url'          => $this->callbackUrl($order, ['status' => 'success']),
                'cancel_url'          => $this->callbackUrl($order, ['status' => 'cancel'])
            ],
            'purchase_units'      => [$paymentUnit]
        ];
    }

    /**
     * Parse result
     *
     * @param $result
     * @return Collection
     */
    protected function parseResult($result)
    {
        return collect(json_decode(json_encode($result), true));
    }

    /**
     * Get money object
     *
     * @param Money $money
     * @return array
     */
    protected function getMoneyObject(Money $money)
    {
        return [
            'currency_code' => $money->getCurrency()->getCurrency(),
            'value'         => $money->getValue()
        ];
    }
}