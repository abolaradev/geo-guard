<?php

namespace Abolaradev\GeoGuard\Exceptions;

use Illuminate\Http\Client\Response;

/**
 * Exception thrown when retrieving the local IP address fails.
 */
class GeoGuardLocalIpException extends AbstractGeoGuardException
{
    /**
     * Create a new local IP exception.
     *
     * @param string $message The exception message.
     * @param Response $response The HTTP response returned by the IP service.
     */
    public function __construct(string $message, Response $response)
    {
        parent::__construct($message, $response);
    }
}