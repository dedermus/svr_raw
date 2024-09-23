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


        RawManager::boot();
    }
}
