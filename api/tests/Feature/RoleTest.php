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
}
