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
        if (! RawManager::boot()) {
            return ;
        }

        $this->app->booted(function () {
            RawManager::routes(__DIR__.'/../routes/web.php');
        });
    }
}
