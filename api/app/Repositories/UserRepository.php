<?php

namespace App\Repositories;

use App\Models\PlayerRole;
use App\User;
use Illuminate\Support\Facades\Auth;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRepository {
    public function __construct()
    {
    }

    public function getAllUsers() {
        return User::paginate(10);
    }

    public function storeUser($request) {
        $req = $request->only(['name', 'email']);
        $password = '';
        if (!array_key_exists('password', $req)) {
            $password = $this->generatePassword();
            $req['password'] = Hash::make($password);
        }
        $user = User::create($req);
        $roleIds = $request->input('roles');
        $user->roles()->sync($roleIds);
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            if ($role->name == 'owner') {
                $this->generateTeamAndPlayers($user);
            }
        }
        return $user;
    }

    public function updateUser($request, $id) {
        $user = User::findOrFail($id);

        if ($request->has('email')) {
            $usersCount = User::where('id', '!=', $user->id)
                ->where('email', 'LIKE', $request->input('email'))
                ->count();
            if ($usersCount > 0) {
                throw new ValidationException('Email entered is already used for another user');
            }
        }

        $user->update($request->only(['name', 'email']));
        $roleIds = $request->input('roles');
        $user->roles()->sync($roleIds);
        return $user;
    }

    public function searchUsers($request, $search) {
        $query = User::query();
        if (!empty($search)) {
            $query = $query->where('email', 'like', $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%');
        }
        if ($request->has('page')) {
            return $query->paginate(10);
        } else {
            $query->get();
        }
    }

    public function searchHavingRole($role) {
        return User::query()->whereHas('roles', function ($query) use($role) {
            $query->where('name', $role);
        })->get();
    }

    public function deleteUser($id) {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);
        if ($user->id == $currentUser->id) {
            return response()->json([
                'message' => 'Could not delete own user from this endpoint'
            ], 403);
        }
        if (!empty($user->team)) {
            $teamRepository = new TeamRepository();
            $teamRepository->deleteTeam($user->team);
        }
        $user->delete();

        return '';
    }

    public function generatePassword() {
        return 'abcdefgh1';
    }

    public function generateTeamAndPlayers($user) {
        $goalkeeper = PlayerRole::where('name', 'LIKE', 'Goalkeeper')->first();
        $defender = PlayerRole::where('name', 'LIKE', 'Defender')->first();
        $midfielder = PlayerRole::where('name', 'LIKE', 'Midfielder')->first();
        $attacker = PlayerRole::where('name', 'LIKE', 'Attacker')->first();

        $team = factory(\App\Models\Team::class)->create([
            'user_id' => $user->id
        ]);

        factory(\App\Models\Player::class, 3)->create([
            'team_id' => $team->id,
            'player_role_id' => $goalkeeper->id
        ]);
        factory(\App\Models\Player::class, 6)->create([
            'team_id' => $team->id,
            'player_role_id' => $defender->id
        ]);
        factory(\App\Models\Player::class, 6)->create([
            'team_id' => $team->id,
            'player_role_id' => $midfielder->id
        ]);
        factory(\App\Models\Player::class, 5)->create([
            'team_id' => $team->id,
            'player_role_id' => $attacker->id
        ]);
    }
}