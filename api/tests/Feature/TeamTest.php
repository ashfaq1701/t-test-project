<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class TeamTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if user can get teams
     *
     * @return void
     */
    public function testUserCanGetTeams()
    {
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test11@test.local",
            "name" => "User Test 11",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::where('email', 'LIKE', 'user.test11@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $token = $this->getTokenForUser($user, 'abcdefgh1');
        $response1 = $this->json('GET', '/api/teams', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response1->assertStatus(200);
        $data = json_decode($response1->getContent(), true);
        $this->assertTrue(count($data['data']) > 0);
    }
}