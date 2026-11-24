<?php

namespace Abolaradev\GeoGuard\Commands;

use Illuminate\Console\Command;

class GeoGuardCommand extends Command
{
    public $signature = 'geo-guard';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
