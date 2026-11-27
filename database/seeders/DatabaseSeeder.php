<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PegawaiSeeder::class,
        ]);

        if (class_exists(\Database\Seeders\DummyDataSeeder::class)) {
            $this->call(\Database\Seeders\DummyDataSeeder::class);
        }
    }
}
