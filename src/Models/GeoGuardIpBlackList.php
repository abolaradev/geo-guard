<?php

namespace Abolaradev\GeoGuard\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GeoGuardIpBlackList extends Model
{
    use HasUuids;

    protected $fillable = ['ip_address','country_code','country_name','path'];

    protected $table = "geo_guard_ip_blacklist";

    protected $primaryKey = "uuid";

    protected $keyType = "string";

    const CREATED_AT = 'banned_at';
    const UPDATED_AT = null;

    protected function countryName(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ucfirst($value)
        );
    }

}