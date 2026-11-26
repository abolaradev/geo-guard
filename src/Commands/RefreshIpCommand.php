<?php

namespace Abolaradev\GeoGuard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\note;

class RefreshIpCommand extends Command
{
    /**
     * The Artisan command signature.
     */
    public $signature = 'geo-guard:refresh-ip';

    /**
     * The Artisan command description.
     */
    public $description = 'Refresh the cached public IP address used in the local environment.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if(app()->environment('local')){
            Cache::forget('geo-guard(local-public-ip)');

            note('The cached public IP address has been successfully cleared.');
            return;
        }

        alert('This command is only available in the local environment.');
    }
}
