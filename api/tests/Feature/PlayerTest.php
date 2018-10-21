<?php

namespace Tests\Feature;

use App\Models\Player;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if user can get players
     *
     * @return void
     */
    public function testUserCanGetPlayers()
    {
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test10@test.local",
            "name" => "User Test 10",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::where('email', 'LIKE', 'user.test10@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $token = $this->getTokenForUser($user, 'abcdefgh1');
        $response1 = $this->json('GET', '/api/players', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response1->assertStatus(200);
        $data = json_decode($response1->getContent(), true);
        $this->assertTrue(count($data['data']) > 0);
    }

    public function testUserCanFilterPlayers() {
        $this->json('POST', '/api/register', [
            "email" => "user.test13@test.local",
            "name" => "User Test 13",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $this->json('POST', '/api/register', [
            "email" => "user.test14@test.local",
            "name" => "User Test 14",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);

        $user1 = User::where('email', 'LIKE', 'user.test13@test.local')->first();
        $user1->confirmation_token = null;
        $user1->save();
        $team = $user1->team;
        $player = $team->players[0];
        $this->assertNotNull($player);

        $user2 = User::where('email', 'LIKE', 'user.test14@test.local')->first();
        $user2->confirmation_token = null;
        $user2->save();

        $totalPlayers = Player::query()->count();
        $token = $this->getTokenForUser($user1, "abcdefgh1");

        $response1 = $this->json('GET', '/api/players', [
            'team' => $team->id
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response1->assertStatus(200);
        $data1 = json_decode($response1->getContent(), true);
        $this->assertTrue(count($data1['data']) > 0);
        $this->assertTrue(count($data1['data']) < $totalPlayers);

        $response2 = $this->json('GET', '/api/players', [
            'team_name' => $team->name,
            'country' => $player->country_id
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $data2 = json_decode($response2->getContent(), true);
        $this->assertTrue(count($data2['data']) > 0);
        $this->assertTrue(count($data2['data']) < $totalPlayers);
    }
}