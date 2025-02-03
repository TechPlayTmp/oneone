<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $fake = false;
        if ($this->command->confirm('Is to put sample data?')) {
            $fake = true;
        }
        
        $this->callWith([
            PermissionsSeeder::class,
            UserSeeder::class,
            MeetingSeeder::class,
            TransactionSeeder::class
        ], ['fake' => $fake]);
    }
}
