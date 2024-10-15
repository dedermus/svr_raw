<?php

namespace Svr\Raw\Seeders;

use Illuminate\Database\Seeder;
use Svr\Raw\Seeders;

class RawSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new Seeders\FromSelexMilkSeeder)->run(1000);
        (new Seeders\FromSelexBeefSeeder)->run(1000);
        (new Seeders\FromSelexSheepSeeder)->run(1000);
    }
}
