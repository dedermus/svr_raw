<?php

namespace Svr\Raw;

use Illuminate\Support\ServiceProvider;

class RawServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(RawManager $extension)
    {

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->registerLangPublishing();
        RawManager::boot();
    }

    /**
     * Register lang resource.
     *
     * @return void
     */
    protected function registerLangPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'svr-raw-lang');
        }
    }
}
