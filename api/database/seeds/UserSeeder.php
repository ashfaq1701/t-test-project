<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = new User;
        $user->name = 'Admin';
        $user->password = Hash::make('abcdefgh1');
        $user->email = 'ashfaq.aws@gmail.com';
        $user->save();

        $admin = Role::findByName('admin');
        $user->assignRole($admin);
    }
}
