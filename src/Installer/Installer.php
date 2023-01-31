<?php

namespace NeoScrypts\Installer;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Installer
{
    /**
     * Filesystem
     *
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * File name
     *
     * @var string
     */
    protected string $path = 'license';

    /**
     * Item code
     *
     * @var string
     */
    protected string $item = '34496505';

    /**
     * License server
     *
     * @var PendingRequest
     */
    protected PendingRequest $client;

    /**
     * Installer constructor
     *
     * @param FilesystemManager $filesystem
     */
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem->disk();
        $this->client = Http::baseUrl('https://license.neoscrypts.com/api/')->acceptJson();
    }

    /**
     * Get license details
     *
     * @return mixed|null
     */
    public function license(): ?array
    {
        if (!$code = $this->load()) {
            return null;
        }

        return Cache::remember("license:$code", Carbon::now()->addDay(), function () use ($code) {
            return $this->client->get("license/$code", ['item' => $this->item])->throw()->json();
        });
    }

    /**
     * Check if license is valid
     *
     * @return bool
     */
    public function hasValidLicense(): bool
    {
        return Arr::get($this->license(), 'item') === $this->item;
    }

    /**
     * Install code
     *
     * @param string $code
     * @return array
     * @throws RequestException
     */
    public function install(string $code): array
    {
        return tap($this->register($code), function () use ($code) {
            $this->save($code);
        });
    }

    /**
     * Register license
     *
     * @param string $code
     * @return array
     * @throws RequestException
     */
    protected function register(string $code): array
    {
        $response = $this->client->get("license/$code", ['item' => $this->item]);

        if ($response->successful()) {
            return $response->json();
        }

        return $this->client->post("license", [
            'code' => $code,
            'item' => $this->item
        ])->throw()->json();
    }

    /**
     * Save license code
     *
     * @param string $code
     */
    protected function save(string $code)
    {
        $this->filesystem->put($this->path, serialize($code));
    }

    /**
     * Load license code
     *
     * @return string|null
     */
    protected function load(): ?string
    {
        if (!$this->installed()) {
            return null;
        }

        return @unserialize($this->filesystem->get($this->path)) ?: null;
    }

    /**
     * Installation status
     *
     * @return bool
     */
    public function installed(): bool
    {
        return $this->filesystem->exists($this->path);
    }
}