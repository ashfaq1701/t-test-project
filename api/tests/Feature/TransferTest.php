<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Transfer;
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

    public function testUserCanTransferPlayer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test31@test.local",
            "name" => "User Test 31",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test31@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(201);
    }

    public function testPlayerCannotBeTransferredTwice() {
        $this->json('POST', '/api/register', [
            "email" => "user.test41@test.local",
            "name" => "User Test 41",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test41@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $token1 = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $response->assertStatus(201);
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);
        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token2 = $this->getTokenForUser($admin, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response->assertStatus(500);
    }

    public function testUserCanAcceptTransfer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test32@test.local",
            "name" => "User Test 32",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user1 = User::where('email', 'LIKE', 'user.test32@test.local')->first();
        $user1->confirmation_token = null;
        $user1->save();
        $team = $user1->team;
        $player = $team->players[0];
        $playerPrice = $player->price;
        $token1 = $this->getTokenForUser($user1, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $data = json_decode($response->getContent(), true);
        $transferArr = $data['data'];
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);

        $this->json('POST', '/api/register', [
            "email" => "user.test33@test.local",
            "name" => "User Test 33",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user2 = User::where('email', 'LIKE', 'user.test33@test.local')->first();
        $user2->confirmation_token = null;
        $user2->save();
        $token2 = $this->getTokenForUser($user2, "abcdefgh1");
        $response2 = $this->json('PUT', '/api/transfers/' . $transferArr['id'], [], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response2->assertStatus(200);
        $transfer = Transfer::find($transferArr['id']);
        $this->assertNotNull($transfer->transfer_completed_at);
        $this->assertEquals($transfer->transferred_to_id, $user2->team->id);

        $playerId = $player->id;
        $player = Player::find($playerId);
        $this->assertTrue($player->price > $playerPrice);
    }

    public function testUserCanNotAcceptTransferWithInsufficientFund() {
        $this->json('POST', '/api/register', [
            "email" => "user.test34@test.local",
            "name" => "User Test 34",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user1 = User::where('email', 'LIKE', 'user.test34@test.local')->first();
        $user1->confirmation_token = null;
        $user1->save();
        $team = $user1->team;
        $player = $team->players[0];
        $token1 = $this->getTokenForUser($user1, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $data = json_decode($response->getContent(), true);
        $transferArr = $data['data'];
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);

        $this->json('POST', '/api/register', [
            "email" => "user.test35@test.local",
            "name" => "User Test 35",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user2 = User::where('email', 'LIKE', 'user.test35@test.local')->first();
        $user2->confirmation_token = null;
        $user2->save();
        $team2 = $user2->team;
        $team2->fund = 50000;
        $team2->save();

        $token2 = $this->getTokenForUser($user2, "abcdefgh1");
        $response2 = $this->json('PUT', '/api/transfers/' . $transferArr['id'], [], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response2->assertStatus(500);
    }

    public function testUserCanRequestUnNotifiedTransfer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test36@test.local",
            "name" => "User Test 36",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user1 = User::where('email', 'LIKE', 'user.test36@test.local')->first();
        $user1->confirmation_token = null;
        $user1->save();
        $team = $user1->team;
        $player1 = $team->players[0];
        $player2 = $team->players[1];
        $token1 = $this->getTokenForUser($user1, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player1->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $data = json_decode($response->getContent(), true);
        $transferArr = $data['data'];
        $this->json('POST', '/api/transfers', [
            'player_id' => $player2->id,
            'asking_price' => 1700000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);

        $this->json('POST', '/api/register', [
            "email" => "user.test37@test.local",
            "name" => "User Test 37",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user2 = User::where('email', 'LIKE', 'user.test37@test.local')->first();
        $user2->confirmation_token = null;
        $user2->save();
        $token2 = $this->getTokenForUser($user2, "abcdefgh1");
        $response2 = $this->json('PUT', '/api/transfers/' . $transferArr['id'], [], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response2->assertStatus(200);
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token2
        ]);

        $token3 = $this->getTokenForUser($user1, "abcdefgh1");
        $response3 = $this->json('GET', '/api/transfers', [
            'type' => 'completed',
            'not_notified' => 1
        ], [
            'Authorization' => 'Bearer ' . $token3
        ]);
        $response3->assertStatus(200);
        $data1 = json_decode($response3->getContent(), true);
        $this->assertEquals(count($data1['data']), 1);
    }

    public function testAdminCanCreateTransfer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test38@test.local",
            "name" => "User Test 38",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test38@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];

        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token = $this->getTokenForUser($admin, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(201);
    }

    public function testAdminCanEditTransfer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test39@test.local",
            "name" => "User Test 39",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test39@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $token1 = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $response->assertStatus(201);
        $data = json_decode($response->getContent(), true);
        $transferArr = $data['data'];
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);
        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token2 = $this->getTokenForUser($admin, "abcdefgh1");
        $response1 = $this->json('PUT', '/api/transfers/' . $transferArr['id'], [
            'asking_price' => 2000000
        ], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response1->assertStatus(200);
    }

    public function testAdminCanDeleteTransfer() {
        $this->json('POST', '/api/register', [
            "email" => "user.test40@test.local",
            "name" => "User Test 40",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $user = User::where('email', 'LIKE', 'user.test40@test.local')->first();
        $user->confirmation_token = null;
        $user->save();
        $team = $user->team;
        $player = $team->players[0];
        $token1 = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/transfers', [
            'player_id' => $player->id,
            'asking_price' => 1500000
        ], [
            'Authentication' => 'Bearer ' . $token1
        ]);
        $response->assertStatus(201);
        $data = json_decode($response->getContent(), true);
        $transferArr = $data['data'];
        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);
        $admin = User::where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token2 = $this->getTokenForUser($admin, "abcdefgh1");
        $response1 = $this->json('DELETE', '/api/transfers/' . $transferArr['id'], [], [
            'Authentication' => 'Bearer ' . $token2
        ]);
        $response1->assertStatus(200);
        $transferId = $transferArr['id'];
        $transfer = Transfer::find($transferId);
        $this->assertNull($transfer);
    }
}