<?php

namespace Svr\Raw\Database\Seeders;

use Illuminate\Database\Seeder;
use Svr\Raw\Models\FromSelexBeef;

class FromSelexBeefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FromSelexBeef::factory()->count(10)->create();
    }
}
