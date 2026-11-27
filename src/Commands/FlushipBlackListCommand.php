<?php

namespace Abolaradev\GeoGuard\Commands;

use Abolaradev\GeoGuard\Models\GeoGuardIpBlackList;
use Illuminate\Console\Command;

use function Laravel\Prompts\note;

class FlushIpBlackListCommand extends Command
{
    /**
     * The Artisan command signature.
     */
    public $signature = 'geo-guard:flush-ip-blacklist';

    /**
     * The Artisan command description.
     */
    public $description = 'Remove all entries from the IP blacklist.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        GeoGuardIpBlackList::truncate();

        note('The IP blacklist has been successfully flushed.');
    }
}
