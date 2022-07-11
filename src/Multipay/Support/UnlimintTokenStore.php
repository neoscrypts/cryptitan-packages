<?php

namespace NeoScrypts\Multipay\Support;

use Cardpay\api\TokensStorageApi;
use Cardpay\ApiException;
use Cardpay\model\ApiTokens;
use Illuminate\Support\Facades\Cache;

class UnlimintTokenStore implements TokensStorageApi
{
    /**
     * Store key
     *
     * @var string key
     */
    protected $key;

    /**
     * UnlimintTokenStore
     *
     * @param $code
     */
    public function __construct($code)
    {
        $this->key = "unlimint_v3_$code";
    }

    /**
     * Save api token
     *
     * @param ApiTokens $apiTokens
     * @return bool
     */
    public function saveApiTokens(ApiTokens $apiTokens)
    {
        return Cache::put($this->key, serialize($apiTokens));
    }

    /**
     * Check if token is stored
     *
     * @return bool
     */
    public function areApiTokensSaved()
    {
        return Cache::has($this->key);
    }

    /**
     * Read tokens from cache
     *
     * @return ApiTokens
     * @throws ApiException
     */
    public function readApiTokens()
    {
        $content = Cache::get($this->key);

        if (empty($content)) {
            throw new ApiException('Invalid tokens cache.');
        }

        $apiTokens = unserialize($content);

        if (!$apiTokens instanceof ApiTokens) {
            throw new ApiException('Unrecognized tokens');
        }

        return $apiTokens;
    }

    /**
     * Remove api token
     *
     * @return bool
     */
    public function deleteApiTokens()
    {
        return Cache::forget($this->key);
    }
}