<?php

namespace Abolaradev\GeoGuard\Middlewares;

use Abolaradev\GeoGuard\Facades\GeoGuard;
use Abolaradev\GeoGuard\Models\GeoGuardIpBlackList;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeoGuardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next , ?string $provider = null): Response
    {
        $ip = GeoGuard::ip();

        $ipIsInBlackList = GeoGuardIpBlackList::where('ip_address','=',$ip)->exists();

        if($ipIsInBlackList){
            abort(403);
        }

        $lookup = GeoGuard::lookup($ip,$provider);   

        $providerInstance = app($lookup->provider);
        $data = $lookup->data;
        $country = $providerInstance->country($data);
  
        if(!in_array($country['country_code'] , GeoGuard::allowedCountries())){

            GeoGuardIpBlackList::create([
                'ip_address' => $ip,
                'country_code' => $country['country_code'],
                'country_name' => $country['country_name'],
                'path' => $request->path()
            ]);
        
            abort(403);
        }

        return $next($request);
    }
}
