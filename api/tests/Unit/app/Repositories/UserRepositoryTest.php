<?php

namespace Tests\Unit\app\Repositories;

use App\Models\Player;
use App\Models\Team;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends BaseTestCase
{
    use RefreshDatabase;

    public $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function testGetAllUsers() {
        $users = $this->userRepository->getAllUsers();
        $this->assertTrue($users->count() > 0);
    }

    public function testStoreUser() {
        $request = $this->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['only', 'input'])
            ->getMock();
        $request->expects($this->any())
            ->method('only')
            ->willReturn(['name' => 'User One', 'email' => 'user.one@test.local']);
        $request->expects($this->any())
            ->method('input')
            ->with($this->equalTo('roles'))
            ->willReturn([3]);
        $user = $this->userRepository->storeUser($request);
        $this->assertNotNull($user);
        $this->assertEquals($user->email, 'user.one@test.local');
    }

    public function testUpdateUser() {
        $user = factory(\App\User::class)->create();
        $request = $this->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['only', 'input', 'has'])
            ->getMock();
        $request->expects($this->any())
            ->method('has')
            ->with('email')
            ->willReturn(true);
        $request->expects($this->any())
            ->method('only')
            ->willReturn(['name' => 'User Two', 'email' => 'user.two@test.local']);
        $request->expects($this->any())
            ->method('input')
            ->will(
                $this->returnCallback(function () {
                    $args = func_get_args();
                    if ($args[0] == 'email') {
                        return 'user.two@test.local';
                    } else if ($args[0] == 'roles') {
                        return [3];
                    }
                })
            );
        $retUser = $this->userRepository->updateUser($request, $user->id);
        $this->assertNotNull($retUser);
        $this->assertEquals($retUser->email, 'user.two@test.local');
    }

    public function testSearchUsers() {
        $request = $this->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['only', 'input', 'has'])
            ->getMock();
        $request->expects($this->any())
            ->method('has')
            ->with('page')
            ->willReturn(true);
        $users = $this->userRepository->searchUsers($request, 'ash');
        $this->assertTrue($users->count() > 0);
    }

    public function testSearchHavingRole() {
        $users = $this->userRepository->searchHavingRole('admin');
        $this->assertTrue($users->count() > 0);
    }

    public function testDeleteUser() {
        $admin = User::query()->where('email', 'LIKE', 'ashfaq.aws@gmail.com')->first();
        Auth::shouldReceive('user')->once()->andreturn($admin);
        $user = factory(\App\User::class)->create();
        $ret = $this->userRepository->deleteUser($user->id);
        $this->assertEquals($ret, '');
    }

    public function generateTeamAndPlayers() {
        $user = factory(\App\User::class)->create();
        $oldTeamCount = Team::query()->count();
        $oldPlayerCount = Player::query()->count();
        $this->userRepository->generateTeamAndPlayers($user);
        $newTeamCount = Team::query()->count();
        $newPlayerCount = Player::query()->count();
        $this->assertEquals($newPlayerCount, $oldPlayerCount + 20);
        $this->assertEquals($newTeamCount, $oldTeamCount + 1);
    }
}
