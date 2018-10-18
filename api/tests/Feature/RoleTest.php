<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;
use App\User;

class RoleTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if admin can get roles
     *
     * @return void
     */
    public function testAdminCanGetRoles()
    {
        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/roles', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if inactive admin can't get roles
     *
     * @return void
     */
    public function testInactiveAdminCantGetRoles()
    {
        $adminRole = $this->getAdminRole();
        Notification::fake();
        $response = $this->json('POST', '/api/register', [
            "email" => "admin.test1@test.local",
            "name" => "Admin Test",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::query()->where('email', 'LIKE', 'admin.test1@test.local')->first();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $this->assertNull($token);
    }

    /**
     * Check if admin can create role
     *
     * @return void
     */
    public function testAdminCanCreateRole()
    {
        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $permission = Permission::query()->first();
        $response = $this->json('POST', '/api/roles', [
           'name' => 'Test',
           'permissions' => [$permission->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(201);
    }

    /**
     * Check if admin can update role
     *
     * @return void
     */
    public function testAdminCanUpdateRole()
    {
        $role = new Role();
        $role->name = 'Test';
        $role->save();

        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $permission = Permission::query()->first();
        $response = $this->json('PUT', '/api/roles/' . $role->id, [
            'name' => 'Test 1',
            'permissions' => [$permission->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if admin can delete role
     *
     * @return void
     */
    public function testAdminCanDeleteRole()
    {
        $role = new Role();
        $role->name = 'Test';
        $role->save();

        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('DELETE', '/api/roles/' . $role->id, [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if others can't get roles
     *
     * @return void
     */
    public function testOthersCantGetRoles()
    {
        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/roles', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't create role
     *
     * @return void
     */
    public function testOthersCantCreateRole()
    {
        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $permission = Permission::query()->first();
        $response = $this->json('POST', '/api/roles', [
            'name' => 'Test',
            'permissions' => [$permission->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't create role
     *
     * @return void
     */
    public function testOthersCantUpdateRole()
    {
        $role = new Role();
        $role->name = 'Test';
        $role->save();

        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $permission = Permission::query()->first();
        $response = $this->json('PUT', '/api/roles/' . $role->id, [
            'name' => 'Test 1',
            'permissions' => [$permission->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't delete role
     *
     * @return void
     */
    public function testOthersCantDeleteRole()
    {
        $role = new Role();
        $role->name = 'Test';
        $role->save();

        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('DELETE', '/api/roles/' . $role->id, [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }
}
