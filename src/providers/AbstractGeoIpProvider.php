<?php

namespace Abolaradev\GeoGuard\Providers;

use Abolaradev\GeoGuard\Exceptions\GeoGuardHttpRequestException;
use Illuminate\Http\Client\Response;

/**
 * Base class for GeoIP providers.
 *
 * Handles provider configuration, HTTP response parsing,
 * and retrieval of the visitor's country information.
 */
abstract class AbstractGeoIpProvider
{
    /**
     * The IP address to look up.
     */
    private string $ip;

    /**
     * The name of the GeoIP provider.
     */
    private string $provider;

    /**
     * Send an HTTP request to the GeoIP provider.
     *
     * @return Response The HTTP response returned by the provider.
     */
    abstract protected function sendHttpRequest(): Response;

    /**
     * Retrieve the country name and ISO code from the provider response data.
     *
     * @param array \$data The response data returned by the provider.
     * @return array The country name and ISO code.
     */
    abstract public function country(array $data): array;

    /**
     * Set the IP address to be used for GeoIP lookup.
     *
     * @param string $ip The IP address.
     * @return self The current instance.
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set the GeoIP provider to be used for the lookup.
     *
     * @param string $provider The provider name.
     * @return self The current instance.
     */
    public function setProvider(string $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Parse the HTTP response and return its JSON data.
     *
     * @param Response $response The HTTP response returned by the provider.
     * @return array The decoded response data.
     *
     * @throws GeoGuardHttpRequestException If the HTTP request fails.
     */
    protected function parseResponse(Response $response): array
    {
        if ($response->failed()) {
            throw new GeoGuardHttpRequestException(
                message: "HTTP Request to GeoGuard Provider ($this->provider) failed ",
                response: $response
            );
        }

        return $response->json();
    }

    /**
     * Retrieve IP details from the configured GeoIP provider.
     *
     * @return array The parsed IP details returned by the provider.
     *
     * @throws GeoGuardHttpRequestException If the HTTP request fails.
     */
    public function getIpDetails(): array
    {
        $response = $this->sendHttpRequest();

        return $this->parseResponse($response);
    }

    /**
     * Get the IP address being looked up.
     *
     * @return string The IP address.
     */
    protected function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Get the API URL configured for the provider.
     *
     * @return string The provider's API URL.
     */
    protected function getApiUrl(): string
    {
        return config('geo-guard.providers.' . $this->provider .'.api_url');
    }

    /**
     * Get the API key configured for the provider.
     *
     * @return string The provider's API key.
     */
    protected function getApiKey(): string
    {
        return config('geo-guard.providers.' . $this->provider .'.api_key');
    }
}