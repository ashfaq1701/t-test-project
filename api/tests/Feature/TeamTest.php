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

    public function testUserCanFilterTeams() {
        $this->json('POST', '/api/register', [
            "email" => "user.test15@test.local",
            "name" => "User Test 15",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $this->json('POST', '/api/register', [
            "email" => "user.test16@test.local",
            "name" => "User Test 16",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);

        $user1 = User::where('email', 'LIKE', 'user.test15@test.local')->first();
        $user1->confirmation_token = null;
        $user1->save();
        $team1 = $user1->team;
        $this->assertNotNull($team1);

        $user2 = User::where('email', 'LIKE', 'user.test16@test.local')->first();
        $user2->confirmation_token = null;
        $user2->save();
        $team2 = $user2->team;
        $this->assertNotNull($team2);

        $token = $this->getTokenForUser($user1, "abcdefgh1");
        $response = $this->json('GET', '/api/teams', [
            'query' => $team2->name,
            'country' => $team2->country_id
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
        $data = json_decode($response->getContent(), true);
        $this->assertTrue(count($data['data']) == 1);
    }
}