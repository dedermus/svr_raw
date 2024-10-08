<?php

namespace Svr\Raw;

use Illuminate\Support\ServiceProvider;

class RawServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // зарегистрировать переводы
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'svr-raw-lang');
        // зарегистрировать миграции пакета
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        RawManager::boot();
    }

}
