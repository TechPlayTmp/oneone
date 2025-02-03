<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{

    public function run(bool $fake = false): void
    {
        if($fake) {
            Transaction::factory(100)->create();
        }
    }

}
