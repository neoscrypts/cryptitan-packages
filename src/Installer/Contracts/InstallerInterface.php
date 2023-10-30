<?php

namespace NeoScrypts\Installer\Contracts;

use Illuminate\Http\Client\RequestException;

interface InstallerInterface
{
    /**
     * Get license details
     *
     * @return mixed|null
     */
    public function license(): ?array;

    /**
     * Check if license is valid
     *
     * @return bool
     */
    public function hasValidLicense(): bool;

    /**
     * Install code
     *
     * @param string $code
     * @return array
     * @throws RequestException
     */
    public function setLicenseCode(string $code): array;

    /**
     * Load license code
     *
     * @return string|null
     */
    public function getLicenseCode(): ?string;

    /**
     * Installation status
     *
     * @return bool
     */
    public function hasLicenseCode(): bool;
}