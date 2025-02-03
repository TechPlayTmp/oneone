<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{

    public function created(User $user): void //obs: revisar se aqui mesmo
    {
        $role = Role::whereName('default')->first();
        $user->roles()->attach($role);
    }
}
