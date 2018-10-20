<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class UsersController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->middleware(['permission:manage_users']);
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->has('query')) {
            $query = $request->input('query');
            $users = $this->userRepository->searchUsers($request, $query);
        } else if ($request->has('role')) {
            $users = $this->userRepository->searchHavingRole($request->input('role'));
        } else {
            $users = $this->userRepository->getAllUsers();
        }
        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users'
        ]);
        $user = $this->userRepository->storeUser($request);
        $this->userRepository->generateTeamAndPlayers($user);
        return new UserResource($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255'
        ]);
        $user = $this->userRepository->updateUser($request, $id);
        return new UserResource($user);
    }

    public function destroy($id)
    {
        return $this->userRepository->deleteUser($id);
    }
}
