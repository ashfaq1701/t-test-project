<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;
use App\User;

class UserTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if admin can get users
     *
     * @return void
     */
    public function testAdminCanGetUsers()
    {
        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/users', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if inactive admin can't get users
     *
     * @return void
     */
    public function testInactiveAdminCantGetUsers()
    {
        $adminRole = $this->getAdminRole();
        Notification::fake();
        $response = $this->json('POST', '/api/register', [
            "email" => "admin.test2@test.local",
            "name" => "Admin Test",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::query()->where('email', 'LIKE', 'admin.test2@test.local')->first();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $this->assertNull($token);
    }

    /**
     * Check if admin can create user
     *
     * @return void
     */
    public function testAdminCanCreateUser()
    {
        Notification::fake();
        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/users', [
            'email' => 'user.xyz1@test.local',
            'name' => 'User XYZ1',
            'roles' => [$otherRole->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(201);
    }

    /**
     * Check if admin can update user
     *
     * @return void
     */
    public function testAdminCanUpdateUser()
    {
        $userUpd = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $userUpd->assignRole($otherRole);

        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('PUT', '/api/users/' . $userUpd->id, [
            'email' => 'user.xyz11@test.local',
            'name' => 'User XYZ11',
            'roles' => [$otherRole->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if admin can delete user
     *
     * @return void
     */
    public function testAdminCanDeleteUser()
    {
        $userUpd = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $userUpd->assignRole($otherRole);

        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('DELETE', '/api/users/' . $userUpd->id, [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if admin can't delete himself
     *
     * @return void
     */
    public function testAdminCantDeleteHimself()
    {
        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('DELETE', '/api/users/' . $user->id, [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't get users
     *
     * @return void
     */
    public function testOthersCantGetUsers()
    {
        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/users', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't create user
     *
     * @return void
     */
    public function testOthersCantCreateUser()
    {
        Notification::fake();
        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/users', [
            'email' => 'user.xyz2@test.local',
            'name' => 'User XYZ2',
            'roles' => [$otherRole->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't update user
     *
     * @return void
     */
    public function testOthersCantUpdateUser()
    {
        $userUpd = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $userUpd->assignRole($otherRole);

        $user = factory(\App\User::class)->create();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('PUT', '/api/users/' . $userUpd->id, [
            'email' => 'user.xyz21@test.local',
            'name' => 'User XYZ21',
            'roles' => [$otherRole->id]
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if others can't delete user
     *
     * @return void
     */
    public function testOthersCantDeleteUser()
    {
        $userUpd = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $userUpd->assignRole($otherRole);

        $user = factory(\App\User::class)->create();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('DELETE', '/api/users/' . $userUpd->id, [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }
}
