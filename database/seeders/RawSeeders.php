<?php

namespace Svr\Raw\Seeders;

use Illuminate\Database\Seeder;
use Svr\Raw\Seeders\FromSelexBeefSeeder;

class RawSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new FromSelexBeefSeeder)->run();
    }
}
