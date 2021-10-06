<?php

namespace Rapidez\Strapi;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Rapidez\Core\RapidezFacade as Rapidez;
use Rapidez\Strapi\ViewDirectives\DynamiczoneDirective;

class StrapiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/strapi.php', 'strapi');

        $this->publishes([
            __DIR__.'/../config/strapi.php' => config_path('strapi.php'),
        ], 'config');

        Blade::directive('dynamiczone', function ($expression) {
            return "<?php echo app('dynamiczone-directive')->render($expression)?>";
        });

        $this->app->bind('dynamiczone-directive', DynamiczoneDirective::class);
    }
}
