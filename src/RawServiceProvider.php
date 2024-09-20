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

//        $this->app->booted(function () {
//            RawManager::routes(__DIR__.'/../routes/web.php');
//        });

        RawManager::boot();
    }
}
