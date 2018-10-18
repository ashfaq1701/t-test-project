<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Dotenv\Exception\ValidationException;

class ProfileRepository {
    public function updateProfile ($request) {
        $user = Auth::user();
        if ($request->has('email')) {
            $usersCount = User::where('id', '!=', $user->id)
                ->where('email', 'LIKE', $request->input('email'))
                ->count();
            if ($usersCount > 0) {
                throw new ValidationException('Email entered is already used for another user');
            }
        }
        $data = $request->only(['name', 'email']);
        if (!empty($request->input('profile_photo_id'))) {
            $data['profile_photo_id'] = $request->input('profile_photo_id');
        }
        $user->update($data);
        if ($request->has('password')) {
            if ((!$request->has('old_password')) || empty($request->input('old_password'))) {
                throw new ValidationException('You must enter the old password to update password');
            }
            if ($request->input('password') == $request->input('old_password')) {
                throw new ValidationException('New password must be different than the old password');
            }
            if (!Hash::check($request->input('old_password'), $user->password)) {
                throw new ValidationException('Entered old password is incorrect');
            }
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();
        return $user;
    }

    public function updatePassword ($request) {
        $user = Auth::user();
        if ($request->input('password') == $request->input('old_password')) {
            throw new ValidationException('New password must be different than the old password');
        }
        if (!Hash::check($request->input('old_password'), $user->password)) {
            throw new ValidationException('Entered old password is incorrect');
        }
        $user->password = Hash::make($request->input('password'));
        $user->password_update_required = null;
        $user->save();
        return $user;
    }

    public function deleteUser () {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $admin = Role::findByName('admin');
            if ($admin->users()->count() == 1) {
                return response()->json([
                    'message' => 'You are the only admin in the system. You must assign another admin before deleting your account'
                ], 403);
            }
        }
        $user->delete();
        return '';
    }
}