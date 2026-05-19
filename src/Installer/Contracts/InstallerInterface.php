<?php

namespace NeoScrypts\Installer\Contracts;

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
     * Store license code
     *
     * @param string $code
     * @return array
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