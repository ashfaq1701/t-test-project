<?php

namespace Tests\Feature;

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
}