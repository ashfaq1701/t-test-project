<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $manageRoles = Permission::create(['name' => 'manage_roles']);
        $managePermissions = Permission::create(['name' => 'manage_permissions']);
        $manageUsers = Permission::create(['name' => 'manage_users']);
        $manageProfile = Permission::create(['name' => 'manage_profile']);
        $managePhotos = Permission::create(['name' => 'manage_photos']);
        $manageAllPhotos = Permission::create(['name' => 'manage_all_photos']);
        $admin = Role::findByName('admin');
        $admin->givePermissionTo([$manageRoles, $managePermissions, $manageUsers, $managePhotos, $manageAllPhotos, $manageProfile]);
        $leagueManager = Role::findByName('league_manager');
        $leagueManager->givePermissionTo([$manageProfile, $managePhotos]);
        $owner = Role::findByName('owner');
        $owner->givePermissionTo([$manageProfile, $managePhotos]);
    }
}
