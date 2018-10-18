<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * User can update profile
     *
     * @return void
     */
    public function testUserCanUpdateProfile()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/profile', [
            'email' => 'user.abcx@test.local',
            'name' => 'User ABCX',
            'old_password' => 'abcdefgh1',
            'password' => 'bcdefgh1',
            'password_confirmation' => 'bcdefgh1'
        ], [
           'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * User can update profile picture
     *
     * @return void
     */
    public function testUserCanUpdateProfilePicture()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        Storage::fake();
        $fakeFile = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('POST', '/api/photos', [
            'file' => $fakeFile
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(201);
        $responseObj = json_decode($response->getContent(), true);
        $fileId = $responseObj['data']['id'];
        $response = $this->json('POST', '/api/profile', [
            'email' => $user->email,
            'name' => $user->name,
            'profile_photo_id' => $fileId
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * User can delete himself
     *
     * @return void
     */
    public function testUserCanDeleteHimself()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('GET', '/api/profile/delete-user', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Only admin can't delete himself
     *
     * @return void
     */
    public function testOnlyAdminCantDeleteHimself()
    {
        $user = User::query()->where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $this->assertTrue(true);
        $response = $this->json('GET', '/api/profile/delete-user', [], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(403);
    }

    /**
     * Auth user can update password
     *
     * @return void
     */
    public function testAuthUserCanUpdatePassword()
    {
        $user = factory(\App\User::class)->create();
        $ownerRole = $this->getOwnerRole();
        $user->assignRole($ownerRole);
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $response = $this->json('POST', '/api/profile/update-password', [
            'old_password' => 'abcdefgh1',
            'password' => 'bcdefgh1',
            'password_confirmation' => 'bcdefgh1'
        ], [
            'Authentication' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }
}
