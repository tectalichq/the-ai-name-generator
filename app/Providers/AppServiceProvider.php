<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Orhanerday\OpenAi\OpenAi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(OpenAi::class, function ($app) {
            return new OpenAi(config('services.openai.token'));
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
