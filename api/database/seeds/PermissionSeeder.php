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

        $createNewPlayer = Permission::create(['name' => 'create_new_player']);
        $createNewTeam = Permission::create(['name' => 'create_new_team']);
        $editPlayers = Permission::create(['name' => 'edit_players']);
        $modifyPlayerRole = Permission::create(['name' => 'modify_player_role']);
        $editTeams = Permission::create(['name' => 'edit_teams']);
        $deletePlayers = Permission::create(['name' => 'delete_players']);
        $deleteTeams = Permission::create(['name' => 'delete_teams']);
        $changeTeamOwnership = Permission::create(['name' => 'change_team_ownership']);
        $transferOwnPlayer = Permission::create(['name' => 'transfer_own_player']);
        $acceptTransferPlayer = Permission::create(['name' => 'accept_transfer_player']);
        $maintainOwnTeam = Permission::create(['name' => 'maintain_own_team']);
        $createNewTransfer = Permission::create(['name' => 'create_new_transfer']);
        $editTransfers = Permission::create(['name' => 'edit_transfers']);
        $deleteTransfers =Permission::create(['name' => 'delete_transfers']);

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([$manageRoles, $manageUsers, $managePhotos, $manageAllPhotos, $manageProfile,
            $createNewPlayer, $createNewTeam, $editPlayers, $editTeams, $deletePlayers, $deleteTeams,
            $changeTeamOwnership, $createNewTransfer, $editTransfers, $deleteTransfers]);

        $leagueManager = Role::findByName('league_manager');
        $leagueManager->givePermissionTo([$manageProfile, $managePhotos, $editTeams, $modifyPlayerRole]);
        $owner = Role::findByName('owner');
        $owner->givePermissionTo([$manageProfile, $managePhotos, $transferOwnPlayer, $acceptTransferPlayer,
            $maintainOwnTeam]);
    }
}
