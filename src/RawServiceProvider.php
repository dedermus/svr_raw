<?php

namespace Svr\Raw;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Svr\Raw\Console\RunRawTests;
use Svr\Raw\Exceptions\ExceptionHandler;
use Svr\Raw\Middleware\ApiValidationErrors;

class RawServiceProvider extends ServiceProvider
{
    protected $commands = [
        RunRawTests::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {


        // Регистрируем routs
        $this->loadRoutesFrom(__DIR__ . '/../routes/Api/api.php');
        $this->register();


        // Регистрируем фабрики моделей
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
        // Регистрируем глобально миддлвар
        $this->registerMiddleware(ApiValidationErrors::class);

        // Регистрируем глобального  обработчик исключений
        $this->withExceptions(new ExceptionHandler());
        RawManager::boot();
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);

    }



    /**
     * Регистрация Middleware
     *
     * @param string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        // $kernel->pushMiddleware($middleware);  // глобально
        $kernel->appendMiddlewareToGroup('api', $middleware); // доббавить мидлвар в группу api
    }

    /**
     * Регистрация обработчика исключений приложения.
     *
     * @param callable|null $using
     * @return $this
     *
     */
    protected function withExceptions(?callable $using = null)
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Illuminate\Foundation\Exceptions\Handler::class
        );

        $using ??= fn() => true;

        $this->app->afterResolving(
            \Illuminate\Foundation\Exceptions\Handler::class,
            fn($handler) => $using(new \Illuminate\Foundation\Configuration\Exceptions($handler)),
        );

        return $this;
    }

}
