<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ProfileRepository;
use App\Http\Resources\User as UserResource;

class ProfileController extends Controller
{
    protected $profileRepository;

    public function __construct(ProfileRepository $profileRepository) {
        $this->profileRepository = $profileRepository;
    }

    public function update(Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'min:8|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/'
        ]);
        return new UserResource($this->profileRepository->updateProfile($request));
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/'
        ]);
        return new UserResource($this->profileRepository->updatePassword($request));
    }

    public function deleteUser() {
        return $this->profileRepository->deleteUser();
    }
}