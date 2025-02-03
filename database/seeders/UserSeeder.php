<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{

    const DATA = [
        ['name' => 'Admin', 'email' => 'admin@oneone.com']
    ];

    public function run(bool $fake = false): void
    {
        foreach (self::DATA as $i => $rowData) {
            $this->createUser($i+1, $rowData);
        }

        if($fake) {
            $role = Role::whereName('default')->first();

            $users = User::factory(100)->create();
            foreach ($users as $user) {
                $user->roles()->attach($role);
            }

        }
    }

    private function createUser(int $id, array $data){
        $password = '12345678'; 
        $data = array_merge($data, ['password' => $password]);
        User::updateOrCreate(['id' => ($id)], $data);

        if($id == 1) {
            Artisan::call('shield:super-admin', [
                '--user' => $id,
                '--panel' => 'admin',
            ]);
        }
    }
}