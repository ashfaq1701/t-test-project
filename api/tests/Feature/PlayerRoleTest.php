<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerRoleTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if user can get player roles
     *
     * @return void
     */
    public function testUserCanGetPlayerRoles()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/player-roles', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }
}