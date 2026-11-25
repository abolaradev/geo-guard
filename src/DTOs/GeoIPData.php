<?php

namespace Abolaradev\GeoGuard\DTOs;

/**
 * Data transfer object containing GeoIP provider information and response data.
 */
readonly class GeoIpData
{
    /**
     * Create a new GeoIP data instance.
     *
     * @param string $provider The name of the GeoIP provider.
     * @param array $data The response data returned by the provider.
     */
    public function __construct(
        public string $provider,
        public array $data = []
    ) {}

    /**
     * Create a GeoIP data instance from provider response data.
     *
     * @param string $provider The name of the GeoIP provider.
     * @param array $data The response data returned by the provider.
     * @return self A new GeoIP data instance.
     */
    public static function fromArray(string $provider, array $data): self
    {
        return new self($provider, $data);
    }
}