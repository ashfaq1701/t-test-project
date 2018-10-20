<?php

namespace Tests\Feature;

use Tests\TestCase;
use Spatie\Permission\Models\Role;

class BaseTestCase extends TestCase
{
    public function setUp() {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        $roleSeeder = new \RoleSeeder();
        $permissionSeeder = new \PermissionSeeder();
        $userSeeder = new \UserSeeder();
        $countrySeeder = new \CountrySeeder();
        $playerRoleSeeder = new \PlayerRoleSeeder();

        $roleSeeder->run();
        $permissionSeeder->run();
        $userSeeder->run();
        $countrySeeder->run();
        $playerRoleSeeder->run();
    }

    public function getTokenForUser($user, $password) {
        $response = $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => $password
        ]);
        $resp = json_decode($response->getContent(), true);
        if (array_key_exists('token', $resp)) {
            return $resp['token'];
        }
        return null;
    }

    public function getOwnerRole() {
        $ownerRole = Role::query()->where('name', 'LIKE', 'owner')->first();
        return $ownerRole;
    }

    public function getAdminRole() {
        $adminRole = Role::query()->where('name', 'LIKE', 'admin')->first();
        return $adminRole;
    }

    public function getOtherRoleThanAdmin() {
        $otherRole = Role::query()->where('name', 'NOT LIKE', 'admin')->first();
        return $otherRole;
    }
}