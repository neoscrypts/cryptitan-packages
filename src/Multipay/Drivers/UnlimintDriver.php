<?php

namespace NeoScrypts\Multipay\Drivers;

use Cardpay\api\AuthApiClient;
use Cardpay\api\PaymentsApi;
use Cardpay\ApiException;
use Cardpay\Configuration;
use Cardpay\model\PaymentRequest;
use DateTime;
use GuzzleHttp\Client;
use NeoScrypts\Multipay\Order;
use NeoScrypts\Multipay\Support\UnlimintTokenStore;

class UnlimintDriver extends AbstractDriver
{
    /**
     * Driver name
     *
     * @var string
     */
    protected $name = "Unlimint";

    /**
     * API Host Url
     *
     * @var string
     */
    protected $host;

    /**
     * Unlimint client
     *
     * @var PaymentsApi
     */
    protected $client;

    /**
     * Initialize Unlimint
     *
     * @param array $config
     * @throws ApiException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if ($this->config('client_env') !== "production") {
            $this->host = "https://sandbox.cardpay.com/api/auth/token";
        } else {
            $this->host = "https://cardpay.com/api/auth/token";
        }

        $terminal = $this->config('terminal');
        $password = $this->config('password');

        $tokenStoreApi = new UnlimintTokenStore($terminal);
        $authApiClient = new AuthApiClient($this->host, $terminal, $password, $tokenStoreApi);

        $apiTokens = $authApiClient->obtainApiTokens();
        $tokenType = $apiTokens->getTokenType();
        $accessToken = $apiTokens->getAccessToken();

        $apiConfig = new Configuration($this->host);
        $apiConfig->setApiKeyPrefix('Authorization', $tokenType);
        $apiConfig->setApiKey('Authorization', $accessToken);

        $this->client = new PaymentsApi($this->host, new Client(), $apiConfig);
    }

    /**
     * @inheritDoc
     */
    public function supportsCurrency(string $currency): bool
    {
        return strtoupper($currency) === strtoupper($this->config('currency'));
    }

    /**
     * @inheritDoc
     * @throws ApiException
     */
    public function request(Order $order, $callback)
    {
        $request = new PaymentRequest($this->buildRequest($order));

        $response = $this->client->createPayment($request);

        return $callback($response->getPaymentData()->getId(), $response->getRedirectUrl());
    }

    /**
     * @inheritDoc
     * @throws ApiException
     */
    public function verify($transactionId)
    {
        $response = $this->client->getPayment($transactionId);

        return $response->getPaymentData()->getStatus() === "COMPLETED";
    }

    /**
     * Build request body
     *
     * @param Order $order
     * @return array
     */
    protected function buildRequest(Order $order)
    {
        $merchantOrder['id'] = $order->getUuid();
        $merchantOrder['description'] = $order->getDescription();

        $request['id'] = microtime(true);
        $request['time'] = new DateTime();

        $paymentData['currency'] = $order->getCurrency()->getCurrency();
        $paymentData['amount'] = $order->getTotalAmount()->getValue();

        $returnUrl['return_url'] = $this->callbackUrl($order);
        $customer['email'] = $order->getEmail();

        return [
            'request'        => $request,
            'merchant_order' => $merchantOrder,
            'return_urls'    => $returnUrl,
            'payment_data'   => $paymentData,
            'customer'       => $customer,
        ];
    }
}