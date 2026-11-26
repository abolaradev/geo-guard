<?php

namespace Abolaradev\GeoGuard\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BigDataCloudProvider extends AbstractGeoIpProvider
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
            'country_name' => $data['country']['name'],
            'country_code' => $data['country']['isoAlpha2']
        ];
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