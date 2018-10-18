<?php

namespace App\Repositories;

use App\Models\RefreshToken;
use Carbon\Carbon;

class RefreshTokenRepository {
    public function __construct() {
    }

    public function create($user, $token) {
        $dateStr = Carbon::now()->addDays(15)->format('Y-m-d H:i:s');
        $refreshTokenStr = str_random(40) . sprintf("%010d", $user->id) . base64_encode($dateStr);
        $refreshToken = new RefreshToken();
        $refreshToken->jwt_token = $token;
        $refreshToken->refresh_token = $refreshTokenStr;
        $refreshToken->expire_at = $dateStr;
        $refreshToken->user()->associate($user);
        $refreshToken->save();
        return $refreshToken;
    }

    public function searchByToken($token) {
        return RefreshToken::where('refresh_token', $token)->first();
    }

    public function blacklistRefreshTokenByJWT($token) {
        $refreshToken = RefreshToken::where('jwt_token', $token)->first();
        if (!empty($refreshToken)) {
            $refreshToken->is_blacklisted = 1;
            $refreshToken->save();
        }
    }
}
