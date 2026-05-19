<?php

namespace NeoScrypts\Installer;

use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use NeoScrypts\Installer\Contracts\InstallerInterface;

class Installer implements InstallerInterface
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
     * Installer constructor
     *
     * @param FactoryContract $filesystem
     */
    public function __construct(FactoryContract $filesystem)
    {
        $this->filesystem = $filesystem->disk();
    }

    /**
     * Get license details
     *
     * @return mixed|null
     */
    public function license(): ?array
    {
        if (!$code = $this->getLicenseCode()) {
            return null;
        }

        return [
            'item' => $this->item,
            'code' => $code,
        ];
    }

    /**
     * Check if license is valid
     *
     * @return bool
     */
    public function hasValidLicense(): bool
    {
        return $this->hasLicenseCode();
    }

    /**
     * Store license code
     *
     * @param string $code
     * @return array
     */
    public function setLicenseCode(string $code): array
    {
        $this->filesystem->put($this->path, serialize($code));

        return [
            'item' => $this->item,
            'code' => $code,
        ];
    }

    /**
     * Load license code
     *
     * @return string|null
     */
    public function getLicenseCode(): ?string
    {
        if (!$this->hasLicenseCode()) {
            return null;
        }

        return @unserialize($this->filesystem->get($this->path)) ?: null;
    }

    /**
     * Installation status
     *
     * @return bool
     */
    public function hasLicenseCode(): bool
    {
        return $this->filesystem->exists($this->path);
    }
}