<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Player;
use App\Models\PlayerRole;
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

    public function testAdminCanCreatePlayer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test27@test.local",
            "name" => "User Test 27",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test27@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $country = Country::query()->first();
        $playerRole = PlayerRole::query()->first();

        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token = $this->getTokenForUser($admin, "abcdefgh1");
        $response = $this->json('POST', '/api/players', [
            'first_name' => 'Test',
            'last_name' => 'Player',
            'age' => 28,
            'price' => 1000000,
            'country_id' => $country->id,
            'team_id' => $team->id,
            'player_role_id' => $playerRole->id
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(201);

    }

    public function testAdminCanUpdatePlayer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test28@test.local",
            "name" => "User Test 28",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test28@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $this->assertNotNull($player);
        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token = $this->getTokenForUser($admin, "abcdefgh1");
        $response = $this->json('PUT', '/api/players/' . $player->id, [
            'first_name' => 'Test',
            'last_name' => 'Player2',
            'age' => 29
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    public function testAdminCanDeletePlayer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test29@test.local",
            "name" => "User Test 29",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test29@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $this->assertNotNull($player);
        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token = $this->getTokenForUser($admin, "abcdefgh1");
        $response = $this->json('DELETE', '/api/players/' . $player->id, [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    public function testLeagueManagerCanUpdatePlayerRole() {
        $this->json('POST', '/api/register', [
            "email" => "user.test30@test.local",
            "name" => "User Test 30",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test30@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $this->assertNotNull($player);

        $playerRole = PlayerRole::query()->first();

        $leagueManager = factory(\App\User::class)->create();
        $leagueManagerRole = $this->getLeagueManagerRole();
        $leagueManager->assignRole($leagueManagerRole);
        $token = $this->getTokenForUser($leagueManager, "abcdefgh1");

        $response = $this->json('PUT', '/api/players/' . $player->id, [
            'player_role_id' => $playerRole->id
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }
}