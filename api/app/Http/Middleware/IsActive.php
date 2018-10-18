<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsActive
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403, 'You must log in to make this request');
        }
        $user = Auth::user();
        if (!empty($user->confirmation_token)) {
            abort(403, 'Account must be confirmed to make this request');
        }
        if ($user->password_update_required == 1) {
            abort(403, 'You need to update your password to make this request');
        }

        return $next($request);
    }
}