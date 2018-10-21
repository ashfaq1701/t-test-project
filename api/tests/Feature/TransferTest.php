<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class TransferTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if user can get transfers
     *
     * @return void
     */
    public function testUserCanGetTransfers()
    {
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test12@test.local",
            "name" => "User Test 12",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::where('email', 'LIKE', 'user.test12@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $token = $this->getTokenForUser($user, 'abcdefgh1');

        $player = $user->team->players[0];
        $this->assertNotNull($player);

        $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);

        $response1 = $this->json('GET', '/api/transfers', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response1->assertStatus(200);
        $data = json_decode($response1->getContent(), true);
        $this->assertTrue(count($data['data']) > 0);
    }

    public function testUserCanFilterTransfers() {
        $this->json('POST', '/api/register', [
            "email" => "user.test17@test.local",
            "name" => "User Test 17",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $this->json('POST', '/api/register', [
            "email" => "user.test18@test.local",
            "name" => "User Test 18",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);

        $user1 = User::where('email', 'LIKE', 'user.test17@test.local')->first();
        $user1->confirmation_token = null;
        $user1->save();
        $player1 = $user1->team->players[0];
        $this->assertNotNull($player1);

        $user2 = User::where('email', 'LIKE', 'user.test18@test.local')->first();
        $user2->confirmation_token = null;
        $user2->save();
        $player2 = $user2->team->players[0];
        $this->assertNotNull($player2);

        $token1 = $this->getTokenForUser($user1, "abcdefgh1");
        $this->json('POST', '/api/transfers', [
            'player_id' => $player1->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);

        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);

        $token2 = $this->getTokenForUser($user2, "abcdefgh1");
        $this->json('POST', '/api/transfers', [
            'player_id' => $player2->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token2
        ]);

        $response1 = $this->json('GET', '/api/transfers', [
            'player_name' => $player1->first_name . ' ' . $player1->last_name
        ], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response1->assertStatus(200);
        $data = json_decode($response1->getContent(), true);
        $this->assertTrue(count($data['data']) == 1);
    }
}