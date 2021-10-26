<?php

namespace NeoScrypts\Installer;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
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
    protected string $item = '';

    /**
     * License server
     *
     * @var PendingRequest
     */
    protected PendingRequest $client;

    /**
     * Installer constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->client = Http::baseUrl('https://license.neoscrypts.com/api/')->acceptJson();
    }

    /**
     * Get license details
     *
     * @return array|null
     * @throws FileNotFoundException
     * @throws RequestException
     */
    public function details(): ?array
    {
        return rescue(function () {
            $expires = Carbon::now()->addDay();
            $code = $this->load();

            return Cache::remember("license.{$code}", $expires, function () use ($code) {
                return $this->verify($code);
            });
        });
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
     * Verify license
     *
     * @param string $code
     * @return array
     * @throws RequestException
     */
    protected function verify(string $code): array
    {
        return $this->client->get("license/{$code}", [
            'item' => $this->item
        ])->throw()->json();
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
     * @return string
     * @throws FileNotFoundException
     */
    protected function load(): string
    {
        return unserialize($this->filesystem->get($this->path));
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