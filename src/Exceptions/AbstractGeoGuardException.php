<?php

namespace Abolaradev\GeoGuard\Exceptions;

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\Response;

/**
 * Base exception for GeoGuard HTTP-related errors.
 */
abstract class AbstractGeoGuardException extends HttpClientException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message The exception message.
     * @param Response $response The HTTP response returned by the provider.
     */
    public function __construct(string $message, Response $response)
    {
        parent::__construct(
            "{$message} | " .
            "HTTP status code: {$response->status()} | " .
            "Reason: {$response->reason()} | " .
            "Details: {$response->body()}"
        );
    }
}