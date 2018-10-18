<?php

namespace App\Repositories;

use App\User;

class ConfirmAccountRepository {
    public function resendConfirmationEmail($request) {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if (empty($user)) {
            return response()->json([
                'message' => 'Please register before attempting account confirmation'
            ], 403);
        }
        if (empty($user->confirmation_token)) {
            return response()->json([
                'message' => 'Account is already confirmed'
            ], 403);
        }
        $user->sendConfirmationEmail();
        return response()->json([
           'message' => 'Confirmation email resent'
        ]);
    }

    public function confirmAccount($request) {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if (empty($user)) {
            return response()->json([
                'message' => 'User with given email could not be found'
            ], 403);
        }
        if (empty($user->confirmation_token)) {
            return response()->json([
                'message' => 'User is already confirmed. No need to confirm further'
            ], 403);
        }
        $token = $request->input('confirmation_token');
        if ($user->confirmation_token != $token) {
            return response()->json([
                'message' => 'Confirmation token provided is incorrect'
            ], 403);
        }
        $user->confirmation_token = null;
        $user->save();
        return response()->json([
            'message' => 'Thank you for confirming your account.'
        ]);
    }
}