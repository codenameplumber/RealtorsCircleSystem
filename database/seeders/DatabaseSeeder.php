<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AgentSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            PropertySeeder::class,
            AgentSeeder::class,
            AgencySeeder::class,
        ]);
    }
}
