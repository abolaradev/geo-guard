<?php

namespace Abolaradev\GeoGuard;

use Abolaradev\GeoGuard\DTOs\GeoIpData;
use Abolaradev\GeoGuard\Providers\LocalIpProvider;

/**
 * Main entry point for retrieving visitor IP and geolocation information.
 */
class GeoGuard
{
    /**
     * Retrieve geolocation details for the visitor's IP address.
     *
     * Uses the specified provider or the default provider configured in GeoGuard.
     *
     * @param string $ip The visitor's IP address.
     * @param string|null $provider The name of the GeoIP provider to use.
     * @return GeoIpData The provider name and retrieved IP details.
     */
    public function lookup(string $ip , ?string $provider = null)
    {
        $selectedProvider = is_null($provider)
                    ? config('geo-guard.default')
                    : $provider;

        $selectedProviderClass = config("geo-guard.providers.$selectedProvider.provider");

        $ip_details = app($selectedProviderClass)->setIp($ip)
                                                ->setProvider($selectedProvider)
                                                ->getIpDetails();

        return GeoIpData::fromArray($selectedProviderClass, $ip_details);
    }

    /**
     * Retrieve the visitor's IP address.
     *
     * In the local environment, retrieves the public IP address
     * using the configured LocalIpProvider instead of the request IP.
     *
     * @return string The visitor's IP address.
     */
    public function ip(): string
    {
        $ip = request()->ip();

        if (app()->environment() == 'local') {
            $ip = app(LocalIpProvider::class)->get();
        }

        return $ip;
    }
}