<?php

namespace Svr\Raw\Seeders;

use Illuminate\Database\Seeder;
use Svr\Raw\Models\FromSelexBeef;

class FromSelexBeefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $limit = 50): void
    {
        $count = $limit;
        $start_time = microtime(true);
        echo "\033[1;33m  " . "Start seeding ==> " . __METHOD__ . " \033[0m\n";
        FromSelexBeef::factory()->count($count)->create();
        echo "\033[0;32m Создали: " . ($count) . " (новых записей)\033[0m\n";
        $end_time = microtime(true);
        echo "\033[0;32m Время выполнения: " . ($end_time - $start_time) . " секунд\033[0m\n";
    }
}
