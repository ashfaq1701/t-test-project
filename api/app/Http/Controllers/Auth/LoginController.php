<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Resources\User;
use App\Repositories\RefreshTokenRepository;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Manager;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Token;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $jwt;
    protected $manager;
    protected $refreshTokenRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt, Manager $manager, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->middleware('guest')->except('logout', 'getUser');
        $this->manager = $manager;
        $this->jwt = $jwt;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if ($token) {
            $this->guard()->setToken($token);

            return true;
        }

        return false;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        $user = Auth::user();
        if (!empty($user->confirmation_token)) {
            return response()->json([
                'message' => 'You need to confirm your account before using it'
            ], 401);
        }
        $refreshToken = $this->refreshTokenRepository->create($user, $token);

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
            'refresh_token' => $refreshToken->refresh_token,
            'refresh_expire_at' => $refreshToken->expire_at
        ];
    }

    public function refresh(Request $request)
    {
        $token = $request->header('Refresh-Token');
        $tokenObj = $this->refreshTokenRepository->searchByToken($token);
        if ($tokenObj->is_used == 1) {
            return response()->json([
                'message' => 'Refresh token already used'
            ], 401);
        }
        if ($tokenObj->is_blacklisted == 1) {
            return response()->json([
                'message' => 'Refresh token blacklisted'
            ], 401);
        }
        if (Carbon::createFromTimeString($tokenObj->expire_at) < Carbon::now()) {
            return response()->json([
                'message' => 'Refresh token expired'
            ], 401);
        }
        try {
            $this->manager->decode(new  Token($tokenObj->jwt_token), false)->toArray();
        } catch (TokenExpiredException $e) {
            $tokenObj->is_used = 1;
            $tokenObj->save();

            $user = $tokenObj->user;
            Auth::login($user);
            return $this->sendLoginResponse($request);
        }
        return response()->json([
            'message' => 'Token already authenticated'
        ], 401);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $token = $this->jwt->getToken()->get();
        $this->refreshTokenRepository->blacklistRefreshTokenByJWT($token);
        $this->guard()->logout();
    }

    /** Get current logged in user */
    public function getUser(Request $request) {
        $user = $request->user();
        return new User($user);
    }
}
