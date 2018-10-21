<?php

namespace Tests\Feature;

use App\Notifications\ConfirmationEmail;
use App\Notifications\ResetPassword;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;

class AuthTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * User can sign up
     *
     * @return void
     */
    public function testUserCanSignUp()
    {
        Notification::fake();
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test@test.local",
            "name" => "User Test",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::where('email', 'LIKE', 'user.test@test.local')->first();
        $this->assertNotNull($user->team);
        $this->assertTrue($user->team->players->count() > 0);
    }

    /**
     * User can sign in
     *
     * @return void
     */
    public function testUserCanSignIn()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => 'abcdefgh1'
        ]);
        $response->assertStatus(200);
    }

    /**
     * User can request password reset
     *
     * @return void
     */
    public function testUserCanRequestPasswordReset()
    {
        Notification::fake();
        $user = factory(\App\User::class)->create();
        $response = $this->json('POST', '/api/password/email', ['email' => $user->email]);
        $response->assertStatus(200);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * User can reset password
     *
     * @return void
     */
    public function testUserCanResetPassword()
    {
        Notification::fake();
        $user = factory(\App\User::class)->create();
        $response = $this->json('POST', '/api/password/email', ['email' => $user->email]);
        $response->assertStatus(200);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use($user) {
            $response1 = $this->json('POST', '/api/password/reset', [
                'email' => $user->email,
                'token' => $notification->token,
                'password' => 'bcdefgh1',
                'password_confirmation' => 'bcdefgh1'
            ]);
            $response1->assertStatus(200);
            return true;
        });
    }

    /**
     * User can confirm account
     *
     * @return void
     */
    public function testUserCanConfirmAccount()
    {
        Notification::fake();
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test4@test.local",
            "name" => "User Test",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::query()->where('email', 'LIKE', 'user.test4@test.local')->first();
        Notification::assertSentTo($user, ConfirmationEmail::class, function ($notification) use($user) {
            $response1 = $this->json('GET', '/api/confirmation', [
                'email' => $user->email,
                'confirmation_token' => $user->confirmation_token
            ]);
            $response1->assertStatus(200);
            return true;
        });
    }

    /**
     * Authenticated user can get own account
     *
     * @return void
     */
    public function testAuthUserCanGetOwnAccount()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/user', [], [
           'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
        $contentObj = json_decode($response->getContent(), true);
        $this->assertEquals($contentObj['data']['id'], $user->id);
    }
}
