<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\User;
use Illuminate\Support\Facades\Notification;

class PermissionTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if admin can get permissions
     *
     * @return void
     */
    public function testAdminCanGetPermissions()
    {
        $user = factory(\App\User::class)->create();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, 'abcdefgh1');
        $response = $this->json('GET', '/api/permissions', [], [
           'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if inactive admin can't get permissions
     *
     * @return void
     */
    public function testInactiveAdminCantGetPermissions()
    {
        Notification::fake();
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test3@test.local",
            "name" => "User Test",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::query()->where('email', 'LIKE', 'user.test3@test.local')->first();
        $adminRole = $this->getAdminRole();
        $user->assignRole($adminRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $this->assertNull($token);
    }

    /**
     * Check if others can't get permissions
     *
     * @return void
     */
    public function testOthersCantGetPermissions()
    {
        $user = factory(\App\User::class)->create();
        $otherRole = $this->getOtherRoleThanAdmin();
        $user->assignRole($otherRole);
        $token = $this->getTokenForUser($user, 'abcdefgh1');
        $response = $this->json('GET', '/api/permissions', [], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }
}
