<?php

namespace Abolaradev\GeoGuard\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Ip2LocationProvider extends AbstractGeoIpProvider
{
    /**
     * Retrieve the country name and ISO code from the provider response data.
     *
     * @param array \$data The response data returned by the provider.
     * @return array The country name and ISO code.
     */
    public function country(array $data): array
    {
        return [
            'country_name' => $data['country_name'],
            'country_code' => $data['country_code']
        ];
    }

    /**
     * Send an HTTP request to the IP2Location API.
     *
     * @return Response The HTTP response returned by the provider.
     */
    protected function sendHttpRequest(): Response
    {
        $ip = $this->getIp();
        $url = $this->getApiUrl();
        $key = $this->getApiKey();

        return Http::acceptJson()
                ->withToken($key)
                ->withQueryParameters(['ip' => $ip])
                ->get($url);
    }
}