<?php

namespace Abolaradev\GeoGuard\Providers;

use Abolaradev\GeoGuard\Exceptions\GeoGuardLocalIpException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Retrieves the public IP address of the local environment.
 */
class LocalIpProvider
{
    /**
     * Retrieve the public IP address using the configured IP service.
     *
     * @return string The public IP address.
     *
     * @throws LocalIpException If the HTTP request fails.
     */
    public function get()
    {
        $urls = config('geo-guard.local_ip.urls');
        $url = Arr::random($urls);
        $http = Http::get($url);

        if ($http->failed()) {
            throw new GeoGuardLocalIpException(
                message: "HTTP request to [{$url}] failed to retrieve the public IP address.",
                response: $http
            );
        }
        
        return trim($http->body());
    }
}