<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\Client;
use Tectalic\OpenAi\Manager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            if (Manager::isGlobal()) {
                // Tectalic OpenAI REST API Client already built.
                return Manager::access();
            }
            // Build the Tectalic OpenAI REST API Client globally.
            $token = config('services.openai.token');
            assert(is_string($token));
            $auth = new Authentication($token);
            $httpClient = new \GuzzleHttp\Client();
            return Manager::build($httpClient, $auth);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
