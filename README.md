# GeoGuard

**A Laravel IP Geolocation and Country-Based Access Control Package.**

GeoGuard is a Laravel package that provides IP geolocation lookup and country-based access control.

You can retrieve location information for any IP address or restrict access to specific routes based on the visitor's country.

## Features

- **IP Geolocation:** Retrieve location information for any IP address using supported GeoIP providers.
- **Country-Based Access Control:** Restrict access to specific routes based on allowed countries.
- **Multiple GeoIP Providers:** Choose between built-in providers or integrate your own.
- **IP Blacklisting:** Automatically blacklist IP addresses belonging to disallowed countries to prevent repeated GeoIP lookups.
- **GeoIP Caching:** Cache lookup results to reduce external API requests and improve performance.
- **Local Public IP Detection:** Retrieve and cache your public IP address in the local environment.
- **Artisan Commands:** Synchronize or flush the IP blacklist and refresh the cached public IP.
- **Extensible Architecture:** Access GeoGuard functionality through a centralized facade.

## Installation

Install the package via Composer:

```bash
composer require abolaradev/geo-guard
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag='geo-guard-config'
```

Publish the migrations:

```bash
php artisan vendor:publish --tag='geo-guard-migrations'
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

After publishing the configuration file, you can find it at `config/geo-guard.php`.

### Default Provider

Set the default GeoIP provider:

```php
'default' => env('GEOGUARD_PROVIDER', 'ip2location'),
```

### Allowed Countries

Specify the countries allowed to access protected routes using ISO 3166-1 alpha-2 country codes:

```php
'allowed_countries' => [
    CountryCode::IR,
],
```

### GeoIP Providers

GeoGuard comes with the following built-in providers:

- `ip2location`
- `bigdatacloud`
- `twoip`

Configure their API credentials in your `.env` file:

```env
GEOGUARD_PROVIDER=ip2location

GEOGUARD_IP2LOCATION_KEY=
GEOGUARD_BIGDATACLOUD_KEY=
GEOGUARD_TWOIP_KEY=
```

Provider classes, API URLs, and API keys can be configured in `config/geo-guard.php`. You can also register your own providers here.

### Cache

GeoGuard caches geolocation lookup results using the IP address and selected provider to avoid unnecessary external requests.

Configure the cache key prefix and expiration time:

```php
'cache' => [
    'base_key' => 'geo-guard-lookup-ip',
    'ttl' => now()->addMonth(),
],
```

### Local IP

Configure the external services used to retrieve the public IP address in the local environment:

```php
'local_ip' => [
    'provider' => LocalIpProvider::class,
    'urls' => [
        'https://api.ipify.org/',
        'https://icanhazip.com/',
        'https://curlmyip.net/',
        'https://api.ip.sb/ip',
    ],
],
```

## IP Address Resolution

GeoGuard retrieves the visitor's IP address differently depending on the environment.

### Local Environment

In local development, `request()->ip()` usually returns a loopback or private network address rather than your actual public IP. This happens because requests are typically made through your local machine or development server.

Therefore, GeoGuard uses an external IP service to retrieve your public IP address:

```php
Http::get('https://api.ipify.org/')->body();
```

The public IP address is cached to avoid repeated requests to external IP services in the local environment.

### Production Environment

In production, GeoGuard retrieves the visitor's IP directly from the incoming request:

```php
request()->ip();
```

**Important:** If your application runs behind a reverse proxy or load balancer, configure Laravel's Trusted Proxies correctly. Otherwise, Laravel may return the proxy's IP instead of the actual visitor's IP.

## Usage

### Retrieving the IP Address

Use the `GeoGuard` facade to retrieve the visitor's IP address:

```php
use Abolaradev\GeoGuard\Facades\GeoGuard;

$ip = GeoGuard::ip();
```

If you change your public IP using a VPN or proxy during local development, clear the cached IP to retrieve the new one:

```bash
php artisan geo-guard:refresh-ip
```

### IP Geolocation Lookup

Use the `lookup()` method to retrieve geolocation information for an IP address:

```php
$ip = GeoGuard::ip();
$location = GeoGuard::lookup($ip);
```

The method accepts two arguments:

- **`$ip` (required):** The IP address to look up.
- **`$provider` (optional):** The GeoIP provider to use. If omitted, GeoGuard uses the default provider configured in `config/geo-guard.php`.

To use a different provider:

```php
$ip = GeoGuard::ip();
$location = GeoGuard::lookup($ip, 'bigdatacloud');
```

The method returns a `\Abolaradev\GeoGuard\DTOs\GeoIpData` object containing the selected provider and its geolocation response data.

Lookup results are cached based on the IP address and selected provider to avoid unnecessary external API requests.

### Allowed Countries

Use the `allowedCountries()` method to retrieve the country codes configured as allowed in `config/geo-guard.php`:

```php
$countries = GeoGuard::allowedCountries();
```

### Country-Based Route Protection

GeoGuard provides a middleware to restrict access to routes based on the visitor's country. The middleware is automatically registered using the `geo-guard` alias.

Apply it to a route or route group:

```php
Route::middleware('geo-guard')->group(function () {
    Route::get('/dashboard', DashboardController::class);
    Route::get('/reports', ReportController::class);
});
```

By default, GeoGuard uses the configured default GeoIP provider. You can also specify a different provider directly in the middleware:

```php
Route::middleware('geo-guard:bigdatacloud')->group(function () {
    Route::get('/dashboard', DashboardController::class);
});
```

When a visitor accesses a protected route, GeoGuard follows these steps:

1. Retrieves the visitor's IP address.
2. Checks whether the IP address already exists in the blacklist.
3. If it is not blacklisted, looks up its geolocation using the selected GeoIP provider.
4. Retrieves the country associated with the IP address.
5. Checks whether the country is included in the allowed countries configured in `config/geo-guard.php`.

For example:

```php
'allowed_countries' => [
    CountryCode::IR,
],
```

If the visitor's country is not allowed, access is denied with an HTTP 403 response, and the visitor's IP address and country information are stored in the `geo_guard_ip_blacklist` table.

### IP Blacklist

Before performing any GeoIP lookup, GeoGuard checks whether the visitor's IP address exists in the blacklist.

If the IP is already blacklisted, access is immediately denied with an HTTP 403 response without sending another request to the GeoIP provider. This avoids unnecessary external API requests for previously identified disallowed IP addresses.

**Blacklist checks are based on the IP address, not the route path.** Therefore, a blacklisted IP address is denied access to every route protected by the `geo-guard` middleware.

Blacklisted IP addresses remain blocked until their records are removed from the database.

#### Synchronizing the Blacklist

If you update the allowed countries configuration, previously blacklisted IP addresses belonging to countries that are now allowed must be removed from the blacklist.

For example, if you add the United States to the allowed countries:

```php
'allowed_countries' => [
    CountryCode::IR,
    CountryCode::US,
],
```

Run the following command to remove blacklist entries associated with countries that are now allowed:

```bash
php artisan geo-guard:sync-ip-blacklist
```

This command only removes entries belonging to newly allowed countries; it does not clear the entire blacklist.

#### Flushing the Blacklist

To remove all entries from the IP blacklist, run:

```bash
php artisan geo-guard:flush-ip-blacklist
```

#### Accessing the Blacklist

You can access the `GeoGuardIpBlackList` model through the `GeoGuard` facade using the `blackList()` method.

It returns an Eloquent query builder, allowing you to retrieve, filter, and manage blacklist records using standard Laravel query methods:

```php
use Abolaradev\GeoGuard\Facades\GeoGuard;

// Retrieve all blacklisted IP addresses
$blacklist = GeoGuard::blackList()->get();

// Filter blacklist records by country
$iranBlacklist = GeoGuard::blackList()
    ->where('country_code', 'IR')
    ->get();

// Delete a specific blacklist entry
GeoGuard::blackList()
    ->where('ip_address', '8.8.8.8')
    ->delete();
```

### Creating a Custom GeoIP Provider

GeoGuard allows you to create your own GeoIP providers using the following Artisan command:

```bash
php artisan geo-guard:provider your-provider
```

The generated provider class will be placed in:

```text
app/GeoGuard/Providers
```

The generated class extends `AbstractGeoIpProvider` and requires you to implement the following methods:

```php
<?php

namespace App\GeoGuard\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Abolaradev\GeoGuard\Providers\AbstractGeoIpProvider;

class YourGeoIpProvider extends AbstractGeoIpProvider
{
    public function country(array $data): array
    {
        return [
            'country_name' => $data[''],
            'country_code' => $data[''],
        ];
    }

    protected function sendHttpRequest(): Response
    {
        $ip = $this->getIp();
        $url = $this->getApiUrl();
        $key = $this->getApiKey();

        // Implement your provider's HTTP request here.
    }
}
```

**Method responsibilities:**

- `sendHttpRequest()`: Sends an HTTP request to the provider using the IP address and API configuration, then returns the HTTP response.
- `country()`: Extracts the country name and ISO 3166-1 alpha-2 code from the provider's response data. The returned array must contain `country_name` and `country_code`.

The example above is a template. You must implement the HTTP request and map the response fields according to your chosen provider's API.

### Registering Your Provider

After creating your provider, register it in `config/geo-guard.php`:

```php
'providers' => [
    // ...

    'your-provider' => [
        'provider' => \App\GeoGuard\Providers\YourGeoIpProvider::class,
        'api_url' => 'https://api.your-provider.com/',
        'api_key' => env('YOUR_PROVIDER_API_KEY'),
    ],
],
```

You can then use your custom provider in three ways:

**1. Set it as the default provider**

```php
'default' => 'your-provider',
```

GeoGuard will use this provider whenever no provider is explicitly specified.

**2. Use it with the `lookup()` method**

```php
$location = GeoGuard::lookup(GeoGuard::ip(), 'your-provider');
```

**3. Use it with the `geo-guard` middleware**

```php
Route::middleware('geo-guard:your-provider')->group(function () {
    Route::get('/dashboard', DashboardController::class);
});
```

If no provider is specified in the middleware, GeoGuard uses the default provider configured in `config/geo-guard.php`.

---

If you find this package useful, please don't forget to give it a ⭐ on GitHub. ❤️

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) for information on how to report security vulnerabilities.

## Credits

- [abolaradev](https://github.com/abolaradev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.