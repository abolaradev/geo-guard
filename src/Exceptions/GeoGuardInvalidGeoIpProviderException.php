<?php

namespace Abolaradev\GeoGuard\Exceptions;

use RuntimeException;

/**
 * Exception thrown when an invalid GeoIP provider is specified.
 */
class GeoGuardInvalidGeoIpProviderException extends RuntimeException
{
    /**
     * Create a new invalid GeoIP provider exception.
     *
     * @param string $provider The name of the invalid GeoIP provider.
     */
    public function __construct(string $provider)
    {
        $message = "The GeoIP provider [{$provider}] is not configured or does not exist.";

        parent::__construct($message);
    }
}