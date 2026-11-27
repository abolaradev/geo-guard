<?php

use Abolaradev\GeoGuard\Enums\CountryCode;
use Abolaradev\GeoGuard\Providers\BigDataCloudProvider;
use Abolaradev\GeoGuard\Providers\Ip2LocationProvider;
use Abolaradev\GeoGuard\Providers\LocalIpProvider;
use Abolaradev\GeoGuard\Providers\TwoIpProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Default GeoIP Provider
    |--------------------------------------------------------------------------
    |
    | Specify the default provider used to retrieve geolocation information
    | when no provider is explicitly specified.
    |
    */

    'default' => env('GEOGUARD_PROVIDER', 'ip2location'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Countries
    |--------------------------------------------------------------------------
    |
    | Define the countries permitted to access routes protected by GeoGuard.
    | Countries must be specified using their ISO 3166-1 alpha-2 codes.
    |
    */

    'allowed_countries' => [
        CountryCode::IR
    ],

    /*
    |--------------------------------------------------------------------------
    | Local IP Provider
    |--------------------------------------------------------------------------
    |
    | Configure the service used to retrieve the public IP address in the
    | local environment. Multiple URLs can be provided as fallback sources.
    |
    */

    'local_ip' => [
        'provider' => LocalIpProvider::class,

        'urls' => [
            'https://api.ipify.org/',
            'https://icanhazip.com/',
            'https://curlmyip.net/',
            'https://api.ip.sb/ip',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for GeoIP lookup results. The cache key is generated
    | using the base key, IP address, and selected provider.
    |
    */

    'cache' => [
        'base_key' => 'geo-guard-lookup-ip',
        'ttl' => now()->addMonth()
    ],

    /*
    |--------------------------------------------------------------------------
    | GeoIP Providers
    |--------------------------------------------------------------------------
    |
    | Configure the available GeoIP providers. Each provider requires a
    | provider class, API endpoint, and API key when applicable.
    |
    | The free request limits are approximate and depend on each provider's
    | current pricing plan and usage policy.
    |
    */

    'providers' => [

        // Free plan: 50,000 requests.
        'ip2location' => [
            'provider' => Ip2LocationProvider::class,
            'api_url' => 'https://api.ip2location.io/',
            'api_key' => env('GEOGUARD_IP2LOCATION_KEY'),
        ],

        // Free plan: 10,000 requests.
        'bigdatacloud' => [
            'provider' => BigDataCloudProvider::class,
            'api_url' => 'https://api-bdc.net/data/ip-geolocation',
            'api_key' => env('GEOGUARD_BIGDATACLOUD_KEY'),
        ],

        // Free plan: 10,000 requests.
        'twoip' => [
            'provider' => TwoIpProvider::class,
            'api_url' => 'https://api.2ip.io/',
            'api_key' => env('GEOGUARD_TWOIP_KEY'),
        ],

        // 'your_provider' => [
        //     'provider' => YourProvider::class,
        //     'api_url' => '',
        //     'api_key' => env('GEOGUARD_YOURPROVIDER_KEY'),
        // ]

    ],

];