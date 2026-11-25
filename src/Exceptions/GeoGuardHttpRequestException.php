<?php

namespace Abolaradev\GeoGuard\Exceptions;

use Illuminate\Http\Client\Response;

/**
 * Exception thrown when an HTTP request to a GeoGuard provider fails.
 */
class GeoGuardHttpRequestException extends AbstractGeoGuardException
{
    /**
     * Create a new HTTP request exception.
     *
     * @param string $message The exception message.
     * @param Response $response The HTTP response returned by the provider.
     */
    public function __construct(string $message, Response $response)
    {
        parent::__construct($message, $response);
    }
}