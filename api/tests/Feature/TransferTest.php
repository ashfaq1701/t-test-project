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
}