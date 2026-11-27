<?php

namespace Abolaradev\GeoGuard;

use Abolaradev\GeoGuard\DTOs\GeoIpData;
use Abolaradev\GeoGuard\Exceptions\GeoGuardInvalidGeoIpProviderException;
use Abolaradev\GeoGuard\Providers\LocalIpProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Main entry point for retrieving visitor IP and geolocation information.
 */
class GeoGuard
{
    /**
     * Retrieve geolocation details for the given IP address using the selected provider.
     *
     * The lookup result is cached based on the IP address and provider to avoid
     * unnecessary HTTP requests to the GeoIP provider.
     *
     * @param string $ip The IP address to look up.
     * @param string|null $provider The name of the GeoIP provider.
     * @return GeoIpData The cached or newly retrieved geolocation data.
     * @throws GeoGuardInvalidGeoIpProviderException if the selected GeoIP provider is invalid.
     */
    public function lookup(string $ip, ?string $provider = null): GeoIpData
    {
        $providers = config("geo-guard.providers");

        $selectedProvider = is_null($provider)
                    ? config('geo-guard.default')
                    : $provider;

        if(!Arr::has($providers,$selectedProvider)){
            throw new GeoGuardInvalidGeoIpProviderException($selectedProvider);
        }

        $cache_key = sprintf(
            "%s(ip:%s|provider:%s)",
            config('geo-guard.cache.base_key'),
            $ip , 
            $selectedProvider
        );

        $cache_ttl = config('geo-guard.cache.ttl');

        return Cache::remember($cache_key, $cache_ttl, function() use($ip,$providers,$selectedProvider){

            $selectedProviderClass = $providers[$selectedProvider]['provider'];
            $ip_details = app($selectedProviderClass)->setIp($ip)
                                                ->setProvider($selectedProvider)
                                                ->getIpDetails();

            return GeoIpData::fromArray($selectedProviderClass, $ip_details);
        });
    }

    /**
     * Retrieve the visitor's IP address.
     *
     * In the local environment, the public IP address is retrieved through
     * LocalIpProvider and cached indefinitely to avoid repeated external HTTP requests.
     * In other environments, the visitor's IP address is obtained directly from the request.
     *
     * @return string The visitor's IP address.
     */
    public function ip(): string
    {
        $ip = request()->ip();

        if (app()->environment('local')) {
            $ip = Cache::rememberForever('geo-guard(local-public-ip)', function () {
                return app(LocalIpProvider::class)->get();
            });
        }

        return $ip;
    }

    /**
     * Retrieve the ISO country codes of all allowed countries.
     *
     * @return array An array of allowed country codes.
     */
    public function allowedCountries(): array
    {
        $allowed_countries = config('geo-guard.allowed_countries');

        return Arr::map($allowed_countries,function($item,$key){
            return $item->value;
        });
    }
}