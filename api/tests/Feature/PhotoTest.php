<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;
use App\User;

class PhotoTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Check if user can upload photo
     *
     * @return void
     */
    public function testUserCanUploadPhoto()
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
        $fileName = $responseObj['data']['file_name'];
        $this->assertTrue(file_exists(public_path('uploads/images/' . $fileName)));
        unlink(public_path('uploads/images/' . $fileName));
    }

    /**
     * Check if inactive user can't upload photo
     *
     * @return void
     */
    public function testInactiveUserCantUploadPhoto()
    {
        Notification::fake();
        $response = $this->json('POST', '/api/register', [
            "email" => "user.test1@test.local",
            "name" => "User Test",
            "password" => "abcdefgh1",
            "password_confirmation" => "abcdefgh1"
        ]);
        $response->assertStatus(201);
        $user = User::query()->where('email', 'LIKE', 'user.test1@test.local')->first();
        $token = $this->getTokenForUser($user, "abcdefgh1");
        $this->assertNull($token);
    }

    /**
     * Check if user can delete own photo
     *
     * @return void
     */
    public function testUserCanDeleteOwnPhoto()
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
        $response = $this->json('DELETE', '/api/photos/' . $fileId, [], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
    }

    /**
     * Check if user can't delete other's photos
     *
     * @return void
     */
    public function testUserCantDeleteOthersPhoto()
    {
        $users = factory(\App\User::class, 2)->create();
        $user1 = $users[0];
        $user2 = $users[1];
        $ownerRole = $this->getOwnerRole();
        $user1->assignRole($ownerRole);
        $user2->assignRole($ownerRole);

        $token1 = $this->getTokenForUser($user1, "abcdefgh1");

        Storage::fake();
        $fakeFile = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('POST', '/api/photos', [
            'file' => $fakeFile
        ], [
            'Authorization' => 'Bearer ' . $token1
        ]);
        $response->assertStatus(201);
        $responseObj = json_decode($response->getContent(), true);
        $fileId = $responseObj['data']['id'];

        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);

        $token2 = $this->getTokenForUser($user2, "abcdefgh1");
        $response = $this->json('DELETE', '/api/photos/' . $fileId, [], [
            'Authorization' => 'Bearer ' . $token2
        ]);
        $response->assertStatus(403);
    }

    /**
     * Check if admin can delete other's photos
     *
     * @return void
     */
    public function testAdminCanDeleteOthersPhoto()
    {
        $users = factory(\App\User::class, 2)->create();
        $user1 = $users[0];
        $user2 = $users[1];
        $ownerRole = $this->getOwnerRole();
        $adminRole = $this->getAdminRole();
        $user1->assignRole($ownerRole);
        $user2->assignRole($adminRole);

        $token1 = $this->getTokenForUser($user1, "abcdefgh1");

        Storage::fake();
        $fakeFile = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('POST', '/api/photos', [
            'file' => $fakeFile
        ], [
            'Authorization' => 'Bearer ' . $token1
        ]);
        $response->assertStatus(201);
        $responseObj = json_decode($response->getContent(), true);
        $fileId = $responseObj['data']['id'];

        $this->json('GET', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token1
        ]);

        $token2 = $this->getTokenForUser($user2, "abcdefgh1");
        $response = $this->json('DELETE', '/api/photos/' . $fileId, [], [
            'Authorization' => 'Bearer ' . $token2
        ]);
        $response->assertStatus(200);
    }
}
