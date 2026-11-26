<?php

namespace Abolaradev\GeoGuard\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TwoIpProvider extends AbstractGeoIpProvider
{
    /**
     * Retrieve the country name and ISO code from the provider response data.
     *
     * @param array $data The response data returned by the provider.
     * @return array The country name and ISO code.
     */
    public function country(array $data): array
    {
        return [
            'country_name' => $data['country'],
            'country_code' => $data['code']
        ];
    }
        
    /**
     * Sends an HTTP request to the TwoIpProvider provider to determine
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
                   ->withUrlParameters(['ip'=>$ip])
                   ->withQueryParameters(['token'=>$key])
                   ->get($url);
    }
}