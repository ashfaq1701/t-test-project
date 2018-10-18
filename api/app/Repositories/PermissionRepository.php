<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository
{
    public function __construct() {
    }

    public function searchPermissions($query) {
        return Permission::where('name', 'like', '%' . $query . '%')->get();
    }
}