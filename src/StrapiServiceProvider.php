<?php

namespace Rapidez\Strapi;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Rapidez\Strapi\ViewDirectives\DynamiczoneDirective;

class StrapiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/strapi.php', 'rapidez.strapi');

        $this->publishes([
            __DIR__.'/../config/rapidez/strapi.php' => config_path('rapidez/strapi.php'),
        ], 'config');

        Blade::directive('dynamiczone', function ($expression) {
            return "<?php echo app('dynamiczone-directive')->render($expression)?>";
        });

        $this->app->bind('dynamiczone-directive', DynamiczoneDirective::class);

        Http::macro('strapi', function ($identifier = null, $password = null, $jwt = null): PendingRequest {
            /** @var PendingRequest $client */
            $client = Http::baseUrl(config('rapidez.strapi.url'))->acceptJson();

            if ($identifier && $password) {
                $jwt = $client->post('/auth/local', [
                    'identifier' => $identifier,
                    'password' => $password,
                ])->throw()->json('jwt');
            }

            if ($jwt) {
                $client = $client->withToken($jwt);
            }

            return $client;
        });
    }
}
