<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

class CountryTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if user can get countries
     *
     * @return void
     */
    public function testUserCanGetCountries()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/countries', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }
}