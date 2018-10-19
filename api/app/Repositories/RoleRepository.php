<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function __construct()
    {
    }

    public function getAllRoles($request) {
        if ($request->has('page')) {
            return Role::paginate(10);
        } else {
            return Role::all();
        }
    }

    public function searchRoles($request, $query) {
        if ($request->has('page')) {
            $roles = Role::where('name', 'like', $query . '%')->paginate();
        } else {
            $roles = Role::where('name', 'like', $query . '%')->get();
        }
        return $roles;
    }
}