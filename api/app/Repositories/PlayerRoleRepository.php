<?php

namespace App\Repositories;

use App\Models\PlayerRole;

class PlayerRoleRepository {
    public function getAllPlayerRoles() {
        return PlayerRole::all();
    }
}