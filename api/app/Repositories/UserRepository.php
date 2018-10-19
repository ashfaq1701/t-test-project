<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Hash;

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

    public function searchUsers($request, $query) {
        if ($request->has('page')) {
            $users = User::where('email', 'like', $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->paginate();
        } else {
            $users = User::where('email', 'like', $query . '%')
                ->orWhere('name', 'like', $query . '%')
                ->get();
        }
        return $users;
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
        $user->delete();

        return '';
    }

    public function generatePassword() {
        return 'abcdefgh1';
    }
}