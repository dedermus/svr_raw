<?php

namespace Svr\Raw\Seeders;

use Illuminate\Database\Seeder;
use Svr\Raw\Models\FromSelexBeef;

class FromSelexBeefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = 500;
        $start_time = microtime(true);
        require_once __DIR__ . '/../src/Models/FromSelexBeef.php';
        echo "\033[1;33 Start seeding: FromSelexBeefSeeder \033[0m\n";
        FromSelexBeef::factory()->count($count)->create();
        echo "\033[0;32mСоздали с использованием фабрики FromSelexBeefSeeder: " . ($count) . " записей\033[0m\n";
        $end_time = microtime(true);
        echo "\033[0;32mВремя выполнения: " . ($end_time - $start_time) . " секунд\033[0m\n";
    }
}
