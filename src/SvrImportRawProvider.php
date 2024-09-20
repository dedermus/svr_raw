<?php

namespace Svr\ImportRaw;

use Illuminate\Support\ServiceProvider;

class SvrImportRawProvider extends ServiceProvider
{


    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPublishing();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'svr-raw-migrations');
        }
    }

}
