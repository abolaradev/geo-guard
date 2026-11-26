<?php

namespace Abolaradev\GeoGuard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

/**
 * Artisan command for generating custom GeoGuard providers.
 */
class GeoGuardCommand extends Command
{
    /**
     * The Artisan command signature.
     */
    public $signature = 'geo-guard:provider {provider}';

    /**
     * The Artisan command description.
     */
    public $description = 'Create a new GeoGuard provider.';

    /**
     * Generate the provider class template.
     *
     * @return string The generated provider class contents.
     */
    private function template(): string
    {
        $provider = $this->provider();

        return <<<php
        <?php

        namespace App\\GeoGuard\\Providers;

        use Illuminate\\Http\\Client\\Response;
        use Illuminate\\Support\\Facades\\Http;
        use Abolaradev\GeoGuard\Providers\AbstractGeoIpProvider;

        class {$provider} extends AbstractGeoIpProvider
        {
            /**
             * Retrieve the country name and ISO code from the provider response data.
             *
             * @param array \$data The response data returned by the provider.
             * @return array The country name and ISO code.
             */
            public function country(array \$data): array
            {
                return [
                    'country_name' => \$data[''],
                    'country_code' => \$data['']
                ];
            }

            /**
             * Sends an HTTP request to this provider to determine
             * which country the given IP address belongs to.
             *
             * @return \\Illuminate\\Http\\Client\\Response The HTTP response returned by the provider.
             */
            protected function sendHttpRequest(): Response
            {
                \$ip = \$this->getIp();
                \$url = \$this->getApiUrl();
                \$key = \$this->getApiKey();

                // return Http::acceptJson()
                //            ->get(\$url)
            }
        }
        php;
    }

    /**
     * Get the provider name from the command argument.
     *
     * @return string The formatted provider name.
     */
    private function provider(): string
    {
        return ucfirst($this->argument('provider'));
    }

    /**
     * Execute the Artisan command and generate the provider class.
     *
     * @return void
     */
    public function handle()
    {
        $app = app_path('GeoGuard\\Providers\\');

        // Create the providers directory if it does not exist.
        if (!File::isDirectory($app)) {
            File::makeDirectory(path: $app, recursive: true);
        }

        $providerPath = $app . $this->provider() . ".php";

        // Prevent overwriting an existing provider.
        if (File::isFile($providerPath)) {
            alert('GeoGuard Provider already exists!');
        } else {
            File::put($providerPath, $this->template());
            info('GeoGuard Provider created successfully!');
        }

        // Display the generated provider file location.
        note("at: [$providerPath]");
    }
}