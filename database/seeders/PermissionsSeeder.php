<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{

    const ROLES = [
        ['name' => 'default', 'guard_name' => 'web']
    ];

    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 1,
        ]);


        $permissions = Permission::where('name', 'like', '%meeting%')->get();
        
        $role = Role::firstOrCreate(self::ROLES[0]);
        $role->permissions()->sync($permissions->pluck('id'));
    }
}
