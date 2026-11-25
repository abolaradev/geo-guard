<?php

namespace Abolaradev\GeoGuard\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BigDataCloudProvider extends AbstractGeoIpProvider
{
    /**
     * Retrieve the country name from the provider response data.
     *
     * The returned country value is used by GeoGuardMiddleware
     * to determine whether the visitor's country is allowed or blocked.
     *
     * @param array $data The response data returned by the GeoIP provider.
     * @return string The country name.
     */
    public function country(array $data): string
    {
        return $data['country']['name'];
    }

    /**
     * Sends an HTTP request to the BigDataCloudProvider provider to determine
     * which country the given IP address belongs to.
     *
     * @return \Illuminate\Http\Client\Response The HTTP response returned by the provider.
     */
    protected function sendHttpRequest(): Response
    {
        $ip = $this->getIp();
        $url = $this->getApiUrl();
        $key = $this->getApiKey();

        return Http::acceptJson()
                   ->withHeader('x-bdc-key',$key)
                   ->withQueryParameters(['ip'=>$ip])
                   ->get($url);
    }
}