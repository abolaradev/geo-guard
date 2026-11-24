<?php

namespace Abolaradev\GeoGuard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Abolaradev\GeoGuard\GeoGuard
 */
class GeoGuard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Abolaradev\GeoGuard\GeoGuard::class;
    }
}
