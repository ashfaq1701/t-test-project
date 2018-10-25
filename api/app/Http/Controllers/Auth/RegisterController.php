<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\UserRepository;
use App\User;
use App\Http\Resources\User As UserResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    public $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        return $user;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/'
        ], [
            'email.unique' => 'Email has already been taken.'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  Request  $request
     * @return UserResource
     */
    protected function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/'
        ], [
            'unique' => 'Email has already been taken.'
        ]);
        $data = request(['name', 'email', 'password']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'confirmation_token' => str_random(40)
        ]);

        $ownerRole = Role::findByName('owner');
        $user->assignRole($ownerRole);
        $user->sendConfirmationEmail();

        $this->userRepository->generateTeamAndPlayers($user);

        return new UserResource($user);
    }
}
