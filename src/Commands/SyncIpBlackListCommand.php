<?php

namespace Abolaradev\GeoGuard\Commands;

use Abolaradev\GeoGuard\Facades\GeoGuard;
use Abolaradev\GeoGuard\Models\GeoGuardIpBlackList;
use Illuminate\Console\Command;

use function Laravel\Prompts\note;

class SyncIpBlackListCommand extends Command
{
    /**
     * The Artisan command signature.
     */
    public $signature = 'geo-guard:sync-ip-blacklist';

    /**
     * The Artisan command description.
     */
    public $description = 'Synchronize the IP blacklist with the currently allowed countries.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $allowedCountries = GeoGuard::allowedCountries();

       foreach($allowedCountries as $allowedCountry){
            GeoGuardIpBlackList::where('country_code','=',$allowedCountry)->delete();
       }

       note('The IP blacklist has been successfully synchronized.');
    }
}
