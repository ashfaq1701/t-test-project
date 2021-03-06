<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $manageRoles = Permission::create(['name' => 'manage_roles']);
        $manageUsers = Permission::create(['name' => 'manage_users']);
        $manageProfile = Permission::create(['name' => 'manage_profile']);
        $managePhotos = Permission::create(['name' => 'manage_photos']);
        $manageAllPhotos = Permission::create(['name' => 'manage_all_photos']);

        $getCountries = Permission::create(['name' => 'get_countries']);
        $getPlayerRoles = Permission::create(['name' => 'get_player_roles']);
        $getPlayers = Permission::create(['name' => 'get_players']);
        $getTeams = Permission::create(['name' => 'get_teams']);
        $getTransfers = Permission::create(['name' => 'get_transfers']);

        $createNewPlayer = Permission::create(['name' => 'create_new_player']);
        $editPlayers = Permission::create(['name' => 'edit_players']);
        $modifyPlayerRole = Permission::create(['name' => 'modify_player_role']);
        $editTeams = Permission::create(['name' => 'edit_teams']);
        $deletePlayers = Permission::create(['name' => 'delete_players']);
        $transferOwnPlayer = Permission::create(['name' => 'transfer_own_player']);
        $acceptTransferPlayer = Permission::create(['name' => 'accept_transfer_player']);
        $maintainOwnTeam = Permission::create(['name' => 'maintain_own_team']);
        $createNewTransfer = Permission::create(['name' => 'create_new_transfer']);
        $editTransfers = Permission::create(['name' => 'edit_transfers']);
        $deleteTransfers =Permission::create(['name' => 'delete_transfers']);

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([$manageRoles, $manageUsers, $managePhotos, $manageAllPhotos, $manageProfile,
            $createNewPlayer, $editPlayers, $editTeams, $deletePlayers, $createNewTransfer, $editTransfers,
            $deleteTransfers, $getCountries, $getPlayerRoles, $getPlayers, $getTeams, $getTransfers]);

        $leagueManager = Role::findByName('league_manager');
        $leagueManager->givePermissionTo([$manageProfile, $managePhotos, $editTeams, $modifyPlayerRole, $getCountries,
            $getPlayerRoles, $getPlayers, $getTeams, $getTransfers]);
        $owner = Role::findByName('owner');
        $owner->givePermissionTo([$manageProfile, $managePhotos, $transferOwnPlayer, $acceptTransferPlayer,
            $maintainOwnTeam, $getCountries, $getPlayerRoles, $getPlayers, $getTeams, $getTransfers]);
    }
}
