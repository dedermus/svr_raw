<?php

namespace Svr\Raw;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class RawServiceProvider extends ServiceProvider
{


    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Регистрируем routs
        $this->loadRoutesFrom(__DIR__ . '/../routes/Api/api.php');


//        Регистрируем фабрики моделей
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Svr\\Raw\\Factories\\' . class_basename($modelName) . 'Factory';
        });

        // зарегистрировать переводы
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'svr-raw-lang');
        // зарегистрировать миграции пакета
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Обработка команды из терминала
        if ($this->app->runningInConsole()) {
            // Обработка команды из терминала
        }

        RawManager::boot();

    }
}
