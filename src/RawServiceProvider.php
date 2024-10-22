<?php

namespace Svr\Raw;


use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Eloquent\Factories\Factory;
class RawServiceProvider extends ServiceProvider
{




    /**
     * {@inheritdoc}
     */
    public function boot()
    {
//        Регистрируем фабрики моделей
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Svr\\Raw\\Factories\\'.class_basename($modelName).'Factory';
        });

        // зарегистрировать переводы
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'svr-raw-lang');
        // зарегистрировать миграции пакета
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Пример обработки команды из терминала
//        if ($this->app->runningInConsole()) {
//            echo "\033[1;33mSeeding из пакета svr/raw: \033[0m\n";
//            echo "\033[1;33mЗапуск провайдера: vendor/svr/raw/src/RawServiceProvider.php is loaded:\033[0m\n";
//            // Это также работает для команды "db:seed" и опции "--seed".
//            // Решение: https://github.com/wowcee/laravel-package-seeds-auto-loader или
//            // https://stackoverflow.com/questions/40095764/laravel-registering-seeds-in-a-package
//            if ($this->isConsoleCommandContains(['db:seed', '--seed'], ['--class', 'help', '-h'])) {
//                $this->addSeedsAfterConsoleCommandFinished();
//            }
//        }

        RawManager::boot();

    }
//
//    /**
//     * Получить значение, указывающее, содержит ли текущая команда в консоли строку в указанных полях.
//     *
//     * @param string|array $contain_options
//     * @param string|array $exclude_options
//     *
//     * @return bool
//     */
//    protected function isConsoleCommandContains($contain_options, $exclude_options = null) : bool
//    {
//        echo "54: RawServiceProvider function isConsoleCommandContains\n";
//        echo "55: RawServiceProvider function isConsoleCommandContains contain_options: ".implode(' ', $contain_options) ."\n";
//        echo "56: RawServiceProvider function isConsoleCommandContains exclude_options: ".implode(' ', $exclude_options) ."\n";
//
//        $args = Request::server('argv', null);
//
//        echo "60: RawServiceProvider function isConsoleCommandContains. Аргументы Request::server : ".implode(' ', $args) ."\n";
//
//        if (is_array($args)) {
//            $command = implode(' ', $args);
//            echo "67: RawServiceProvider function isConsoleCommandContains. command : ".$command ."\n";
//            echo "68: Str::contains : ".Str::contains($command, $contain_options) ."\n";
//            echo "69: !Str::contains : ".Str::contains($command, $exclude_options) ."\n";
//            if (
//                Str::contains($command, $contain_options) &&
//                ($exclude_options == null || !Str::contains($command, $exclude_options))
//            ) {
//                echo "74: RawServiceProvider function return true\n";
//                return true;
//            }
//        }
//        echo "75: RawServiceProvider function return false\n";
//        return false;
//    }
//
//    /**
//     * Добавить начальные значения из $seed_path после завершения текущей команды в консоли.
//     */
//    protected function addSeedsAfterConsoleCommandFinished()
//    {
//        echo "84: RawServiceProvider function addSeedsAfterConsoleCommandFinished\n";
//        Event::listen(CommandFinished::class, function (CommandFinished $event) {
//            // Принимать команду только в консоли,
//            // исключить все команды из метода Artisan::call().
//            if ($event->output instanceof ConsoleOutput) {
//                $this->addSeedsFrom(__DIR__ . '/../database/seeders/');
//            }
//        });
//    }
//
//
//    /**
//     * Register seeds.
//     *
//     * @param string  $seeds_path
//     * @return void
//     */
//    protected function addSeedsFrom($seeds_path)
//    {
//        echo "103: RawServiceProvider function addSeedsFrom. Аргумент:{$seeds_path}\n";
//
//        $file_names = glob( $seeds_path . '/*.php');
//        if ($file_names && file_exists($file_names[0])) {
//            echo "107: File exists: {$file_names[0]}"."\n";
//        } else {
//            echo "109: File not exists \n";
//        }
//
//        echo "107: RawServiceProvider function addSeedsFrom. file_names: ".implode(' ', $file_names) ."\n";
//        foreach ($file_names as $filename)
//        {
//            $classes = $this->getClassesFromFile($filename);
//            echo "117: RawServiceProvider function addSeedsFrom. classes: ".implode(' ', $classes) ."\n";
//            foreach ($classes as $_class) {
////                $_class = 'RawSeeders';
//                echo "\033[1;33m119: Seeding:\033[0m {$_class}\n";
//                $startTime = microtime(true);
//                // Проверим наличие класса по неймспейсу.
//                if (class_exists($_class)) {
//                    echo "\033[1;33m125: Class {$_class} found \033[0m\n";
//                }else{
//                    echo "\033[1;31m127: Class {$_class} not found! \031[0m\n";
//                }
//                require_once $filename;
//                Artisan::call('db:seed', [ '--class' => $_class, '--force' => '' ]);
//                $runTime = round(microtime(true) - $startTime, 2);
//                echo "\033[0;32m Seeded:\033[0m {$_class} ({$runTime} seconds)\n";
//            }
//        }
//    }
//
//
//    /**
//     * Get full class names declared in the specified file.
//     *
//     * @param string $filename
//     * @return array an array of class names.
//     */
//    private function getClassesFromFile(string $filename) : array
//    {
//        echo "RawServiceProvider function getClassesFromFile. Аргумент: {$filename}\n";
//        // Get namespace of class (if vary)
//        $namespace = "";
//        $lines = file($filename);
//        $namespaceLines = preg_grep('/^namespace /', $lines);
//        if (is_array($namespaceLines)) {
//            $namespaceLine = array_shift($namespaceLines);
//            $match = array();
//            preg_match('/^namespace (.*);$/', $namespaceLine, $match);
//            $namespace = array_pop($match);
//        }
//
//        // Get name of all class has in the file.
//        $classes = array();
//        $php_code = file_get_contents($filename);
//        $tokens = token_get_all($php_code);
//        $count = count($tokens);
//        for ($i = 2; $i < $count; $i++) {
//            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
//                $class_name = $tokens[$i][1];
//                if ($namespace !== "") {
//                    $classes[] = $namespace . "\\$class_name";
//                } else {
//                    $classes[] = $class_name;
//                }
//            }
//        }
//
//        return $classes;
//    }
}
