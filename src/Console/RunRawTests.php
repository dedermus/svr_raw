<?php

namespace Svr\Raw\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunRawTests extends Command
{
    protected $signature = 'svr/raw:tests {testsuite}';

    protected $description = 'Запуск тестов svr/raw';

    protected $help = 'параметр testsuite берется из файла phpunit.xml';


    public function handle()
    {
        $testsuite = $this->argument('testsuite');

        // Конструктор консольной команды
        $process = new Process([
            './vendor/bin/phpunit',
            '--configuration',
            './vendor/svr/raw/phpunit.xml',
            '--testsuite',
            $testsuite
        ]);

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->error($buffer);
            } else {
                $this->line($buffer);
            }
        });
    }
}
